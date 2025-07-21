<?php

namespace App\Http\Controllers;

use App\Models\ServiceBooking;
use App\Models\ServiceItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ServiceBookingController extends Controller
{
    /**
     * Display a listing of service bookings
     */
    public function index()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $serviceBookings = ServiceBooking::where('business_id', $business->id)
            ->with(['customer', 'serviceItems.service', 'serviceItems.staff'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('service-bookings.index', compact('serviceBookings'));
    }

    /**
     * Show the form for creating a new service booking
     */
    public function create()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $services = Service::where('business_id', $business->id)->get();
        $staff = Staff::where('business_id', $business->id)->get();
        $customers = Customer::where('business_id', $business->id)->get();

        return view('service-bookings.create', compact('services', 'staff', 'customers'));
    }

    /**
     * Store a newly created service booking
     */
    public function store(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $request->validate([
            'selected_services' => 'required|array|min:1',
            'selected_services.*' => 'exists:services,id',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.staff_id' => 'required|exists:staff,id',
            'services.*.amount' => 'required|numeric|min:0',
            'services.*.commission' => 'required|numeric|min:0',
            'services.*.is_complimentary' => 'nullable|string|in:true,false',
            'services.*.parent_service_id' => 'nullable|exists:services,id',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'required|string|in:cash,mpesa,card,bank_transfer,other',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $business) {
            // Calculate total amount (excluding complimentary services)
            $totalAmount = 0;
            $serviceItems = [];
            
            foreach ($request->input('services') as $index => $serviceData) {
                $isComplimentary = isset($serviceData['is_complimentary']) && $serviceData['is_complimentary'] === 'true';
                $service = Service::find($serviceData['service_id']);
                
                // Only add to total if not complimentary
                if (!$isComplimentary) {
                    $totalAmount += $serviceData['amount'];
                }
                
                $serviceItems[] = [
                    'service_id' => $serviceData['service_id'],
                    'staff_id' => $serviceData['staff_id'],
                    'amount' => $serviceData['amount'],
                    'commission_rate' => $service->commission_rate,
                    'commission_amount' => $serviceData['commission'],
                    'sequence_order' => $index + 1,
                    'notes' => $isComplimentary ? 'Complimentary service' : $request->input('notes'),
                ];
            }

            // Create the service booking
            $serviceBooking = ServiceBooking::create([
                'business_id' => $business->id,
                'customer_id' => $request->input('customer_id'),
                'total_amount' => $totalAmount,
                'payment_status' => 'paid',
                'payment_method' => $request->input('payment_method'),
                'notes' => $request->input('notes'),
            ]);

            // Create service items with commission data
            foreach ($serviceItems as $itemData) {
                ServiceItem::create(array_merge($itemData, [
                    'service_booking_id' => $serviceBooking->id,
                ]));
            }
        });

        $complimentaryCount = count(array_filter($request->input('services'), function($service) {
            return isset($service['is_complimentary']) && $service['is_complimentary'] === 'true';
        }));
        
        $regularCount = count($request->input('services')) - $complimentaryCount;
        $message = "Service payment recorded successfully for {$regularCount} service(s)";
        if ($complimentaryCount > 0) {
            $message .= " (+ {$complimentaryCount} complimentary service(s))";
        }
        $message .= '. Payment method: ' . ucfirst($request->input('payment_method'));

        return redirect()->route('service-bookings.index')
            ->with('success', $message);
    }

    /**
     * Display the specified service booking
     */
    public function show(ServiceBooking $serviceBooking)
    {
        $this->authorize('view', $serviceBooking);
        $serviceBooking->load(['customer', 'serviceItems.service', 'serviceItems.staff']);
        
        return view('service-bookings.show', compact('serviceBooking'));
    }

    /**
     * Show the form for editing the specified service booking
     */
    public function edit(ServiceBooking $serviceBooking)
    {
        $this->authorize('update', $serviceBooking);
        
        $business = Auth::user()->business;
        $services = Service::where('business_id', $business->id)->get();
        $staff = Staff::where('business_id', $business->id)->get();
        $customers = Customer::where('business_id', $business->id)->get();

        $serviceBooking->load(['serviceItems.service', 'serviceItems.staff']);

        return view('service-bookings.edit', compact('serviceBooking', 'services', 'staff', 'customers'));
    }

    /**
     * Update the specified service booking
     */
    public function update(Request $request, ServiceBooking $serviceBooking)
    {
        $this->authorize('update', $serviceBooking);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'customer_id' => 'nullable|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'payment_status' => 'required|string|in:pending,paid,cancelled',
            'payment_method' => 'nullable|string|in:cash,mpesa,card,bank_transfer,other',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $serviceBooking) {
            // Update the service booking
            $serviceBooking->update([
                'customer_id' => $request->input('customer_id'),
                'total_amount' => $request->input('amount'),
                'payment_status' => $request->input('payment_status'),
                'payment_method' => $request->input('payment_method'),
                'notes' => $request->input('notes'),
            ]);

            // Delete existing service items and create new one
            $serviceBooking->serviceItems()->delete();

            ServiceItem::create([
                'service_booking_id' => $serviceBooking->id,
                'service_id' => $request->input('service_id'),
                'staff_id' => $request->input('staff_id'),
                'amount' => $request->input('amount'),
                'sequence_order' => 1,
                'notes' => $request->input('notes'),
            ]);
        });

        return redirect()->route('service-bookings.index')
            ->with('success', 'Service booking updated successfully.');
    }

    /**
     * Remove the specified service booking
     */
    public function destroy(Request $request, ServiceBooking $serviceBooking)
    {
        $this->authorize('delete', $serviceBooking);

        // Validate password if this is an AJAX request
        if ($request->ajax()) {
            $request->validate([
                'password' => 'required|string'
            ]);

            // Verify the user's password
            $user = Auth::user();
            if (!Hash::check($request->input('password'), $user->getAuthPassword())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password. Please check your password and try again.'
                ], 422);
            }
        }

        try {
            DB::transaction(function () use ($serviceBooking) {
                $serviceBooking->serviceItems()->delete();
                $serviceBooking->delete();
            });

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service booking deleted successfully.'
                ]);
            }

            return redirect()->route('service-bookings.index')
                ->with('success', 'Service booking deleted successfully.');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the service booking. Please try again.'
                ], 500);
            }

            return redirect()->route('service-bookings.index')
                ->with('error', 'An error occurred while deleting the service booking.');
        }
    }

    /**
     * Add a service to an existing service booking
     */
    public function addService(Request $request, ServiceBooking $serviceBooking)
    {
        $this->authorize('update', $serviceBooking);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Get next sequence order
        $nextOrder = $serviceBooking->serviceItems()->max('sequence_order') + 1;

        ServiceItem::create([
            'service_booking_id' => $serviceBooking->id,
            'service_id' => $request->input('service_id'),
            'staff_id' => $request->input('staff_id'),
            'amount' => $request->input('amount'),
            'sequence_order' => $nextOrder,
            'notes' => $request->input('notes'),
        ]);

        // Update total amount
        $totalAmount = $serviceBooking->serviceItems()->sum('amount');
        $serviceBooking->update(['total_amount' => $totalAmount]);

        return redirect()->route('service-bookings.show', $serviceBooking)
            ->with('success', 'Service added successfully.');
    }

    /**
     * Remove a service item from a service booking
     */
    public function removeService(ServiceItem $serviceItem)
    {
        $serviceBooking = $serviceItem->serviceBooking;
        $this->authorize('update', $serviceBooking);

        $serviceItem->delete();

        // Update total amount
        $totalAmount = $serviceBooking->serviceItems()->sum('amount');
        $serviceBooking->update(['total_amount' => $totalAmount]);

        return redirect()->route('service-bookings.show', $serviceBooking)
            ->with('success', 'Service removed successfully.');
    }

    /**
     * Mark a service booking as complete
     */
    public function complete(ServiceBooking $serviceBooking)
    {
        $this->authorize('update', $serviceBooking);

        $serviceBooking->update([
            'payment_status' => 'paid',
        ]);

        return redirect()->route('service-bookings.show', $serviceBooking)
            ->with('success', 'Service booking marked as complete.');
    }
}
