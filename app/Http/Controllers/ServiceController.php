<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\ClaudeAPIService;

class ServiceController extends Controller
{
    protected $claudeService;

    public function __construct(ClaudeAPIService $claudeService)
    {
        $this->claudeService = $claudeService;
    }
    public function index()
    {
        $user = Auth::user();
        if (!$user->business) {
            return redirect()->route('business.choose-type')
                ->with('error', 'Please set up your business first.');
        }
        
        $services = Service::where('business_id', $user->business->id)->get();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $user = Auth::user();
        if (!$user->business) {
            return redirect()->route('business.choose-type')
                ->with('error', 'Please set up your business first.');
        }
        
        // Get existing services for the current business to use as parent services or bundled services
        $services = Service::where('business_id', $user->business->id)->get();
        
        return view('services.create', compact('services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_bundle_trigger' => 'nullable|boolean',
            'bundled_services' => 'nullable|array',
            'bundled_services.*' => 'exists:services,id',
            'is_complimentary' => 'nullable|boolean',
            'parent_service_id' => 'nullable|exists:services,id',
        ]);
        
        $data['business_id'] = Auth::user()->business->id;
        
        // Handle boolean values
        $data['is_bundle_trigger'] = $request->has('is_bundle_trigger');
        $data['is_complimentary'] = $request->has('is_complimentary');
        
        // Handle bundled services array
        if ($request->has('bundled_services')) {
            $data['bundled_services'] = $request->input('bundled_services');
        }
        
        Service::create($data);
        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        
        $user = Auth::user();
        // Get other services for bundling relationships (exclude current service)
        $services = Service::where('business_id', $user->business->id)
                          ->where('id', '!=', $service->id)
                          ->get();
        
        return view('services.edit', compact('service', 'services'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);
        $service->update($data);
        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Request $request, Service $service)
    {
        // Add debug logging at the very beginning
        Log::info('Service destroy method called', [
            'request_method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'service_id' => $service->id ?? 'null',
            'headers' => $request->headers->all()
        ]);
        
        try {
            $user = Auth::user();
            $userBusiness = $user->business;
            
            if (!$userBusiness) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No business associated with your account. Please contact support.'
                    ], 403);
                }
                return redirect()->route('services.index')
                    ->with('error', 'No business associated with your account.');
            }

            // Check if the service belongs to the user's business
            if ($userBusiness->id !== $service->business_id) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are not authorized to delete this service. It belongs to a different business.'
                    ], 403);
                }
                return redirect()->route('services.index')
                    ->with('error', 'You are not authorized to delete this service.');
            }

            // Validate password if this is an AJAX request
            if ($request->ajax()) {
                $request->validate([
                    'password' => 'required|string'
                ]);

                // Verify the user's password
                if (!Hash::check($request->input('password'), $user->getAuthPassword())) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid password. Please check your password and try again.'
                    ], 422);
                }
            }

            // Check if service has existing bookings with improved error handling
            try {
                // Use a more robust approach to count related records
                $bookingCount = 0;
                if (method_exists($service, 'serviceItems')) {
                    $bookingCount = $service->serviceItems()->count();
                } else {
                    // Fallback: direct database query if relationship doesn't work
                    $bookingCount = \App\Models\ServiceItem::where('service_id', $service->getKey())->count();
                }
                
                if ($bookingCount > 0) {
                    $message = "Cannot delete this service. It has {$bookingCount} existing booking(s). Please remove all bookings first or contact support.";
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $message
                        ], 422);
                    }

                    return redirect()->route('services.index')
                        ->with('error', $message);
                }
            } catch (\Exception $relationshipError) {
                // If there's an issue with the relationship, log it but allow deletion
                Log::warning('ServiceItems relationship check failed', [
                    'service_id' => $service->getKey(),
                    'error' => $relationshipError->getMessage()
                ]);
                // Continue with deletion since we can't verify bookings
            }

            $service->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service deleted successfully.'
                ]);
            }

            return redirect()->route('services.index')
                ->with('success', 'Service deleted successfully.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
                ], 422);
            }
            return back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Service deletion error', [
                'error' => $e->getMessage(),
                'service_id' => $service->id ?? 'unknown',
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred while deleting the service. Please try again.'
                ], 500);
            }

            return redirect()->route('services.index')
                ->with('error', 'An error occurred while deleting the service.');
        }
    }

    /**
     * Enhance service description using AI
     */
    public function enhanceDescription(Request $request)
    {
        $request->validate([
            'description' => 'required|string|min:10',
            'service_name' => 'required|string',
            'duration' => 'nullable|integer',
        ]);

        try {
            $enhancedDescription = $this->claudeService->enhanceServiceDescription(
                $request->description,
                $request->service_name,
                $request->duration
            );

            return response()->json([
                'success' => true,
                'enhanced_description' => $enhancedDescription,
                'original_description' => $request->description,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance description: ' . $e->getMessage(),
            ], 500);
        }
    }
} 