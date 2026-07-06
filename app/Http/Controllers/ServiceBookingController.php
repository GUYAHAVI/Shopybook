<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesCurrentBusiness;
use App\Models\ServiceBooking;
use App\Models\ServiceItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use App\Models\Cost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Business; // Added this import for the new method
use App\Services\NotificationService;
use Carbon\Carbon;

class ServiceBookingController extends Controller
{
    use ResolvesCurrentBusiness;

    /**
     * Display a listing of service bookings
     */
    public function index()
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
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
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
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
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
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
            'discount_type' => 'nullable|string|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
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

            // Calculate discount amounts
            $discountType = $request->input('discount_type', 'none');
            $discountValue = $request->input('discount_value', 0);
            $subtotal = $totalAmount;
            
            $discountAmount = 0;
            if ($discountType === 'percentage' && $discountValue > 0) {
                $discountAmount = ($subtotal * $discountValue) / 100;
            } elseif ($discountType === 'fixed' && $discountValue > 0) {
                $discountAmount = min($discountValue, $subtotal); // Don't exceed subtotal
            }
            
            $finalAmount = max(0, $subtotal - $discountAmount); // Don't go below 0

            // Create the service booking
            $serviceBooking = ServiceBooking::create([
                'business_id' => $business->id,
                'customer_id' => $request->input('customer_id'),
                'total_amount' => $totalAmount,
                'discount_type' => $discountType,
                'discount_value' => $discountValue > 0 ? $discountValue : null,
                'discount_amount' => $discountAmount > 0 ? $discountAmount : null,
                'subtotal' => $subtotal,
                'final_amount' => $finalAmount,
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

            // Send notifications after successful booking creation
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewServiceBooking($serviceBooking);
            } catch (\Exception $e) {
                // Log error but don't fail the booking creation
                \Log::error('Failed to send service booking notifications: ' . $e->getMessage());
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
        
        $business = $this->currentBusiness();
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

    /**
     * Store a public service booking from business show page
     */
    public function storePublic(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'service_id' => 'required|exists:services,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'special_requirements' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // Get the business and service
                $business = Business::findOrFail($request->business_id);
                $service = Service::findOrFail($request->service_id);
                
                // Verify the service belongs to the business
                if ($service->business_id !== $business->id) {
                    throw new \Exception('Service does not belong to this business.');
                }

                // Create or find customer
                $customer = Customer::firstOrCreate(
                    [
                        'business_id' => $business->id,
                        'phone' => $request->customer_phone
                    ],
                    [
                        'name' => $request->customer_name,
                        'email' => $request->customer_email
                    ]
                );

                // Create the service booking without staff assignment
                $serviceBooking = ServiceBooking::create([
                    'business_id' => $business->id,
                    'customer_id' => $customer->id,
                    'total_amount' => $service->price,
                    'subtotal' => $service->price,
                    'final_amount' => $service->price,
                    'payment_status' => 'pending',
                    'payment_method' => 'pending',
                    'notes' => $request->special_requirements,
                    'service_date' => $request->booking_date . ' ' . $request->booking_time,
                ]);

                // Create the service item without staff assignment
                ServiceItem::create([
                    'service_booking_id' => $serviceBooking->id,
                    'service_id' => $service->id,
                    'staff_id' => null, // No staff assigned initially
                    'amount' => $service->price,
                    'commission_rate' => $service->commission_rate,
                    'commission_amount' => ($service->price * $service->commission_rate) / 100,
                    'sequence_order' => 1,
                    'notes' => $request->special_requirements,
                ]);

                // Send notifications after successful booking creation
                try {
                    $notificationService = new \App\Services\NotificationService();
                    $notificationService->notifyNewServiceBooking($serviceBooking);
                    Log::info('Public service booking notification sent', [
                        'business_id' => $business->id,
                        'booking_id' => $serviceBooking->id,
                        'email' => $business->user->email ?? $business->email
                    ]);
                } catch (\Exception $e) {
                    // Log error but don't fail the booking creation
                    Log::error('Failed to send public service booking notifications: ' . $e->getMessage());
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Service booked successfully! The business will assign staff and contact you to confirm your appointment.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error booking service: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign staff to a service item
     */
    public function assignStaff(Request $request)
    {
        $request->validate([
            'service_item_id' => 'required|exists:service_items,id',
            'staff_id' => 'required|exists:staff,id',
        ]);

        try {
            $serviceItem = ServiceItem::findOrFail($request->service_item_id);
            
            // Check if the service item belongs to the current business
            $business = $this->currentBusiness();
            if ($serviceItem->serviceBooking->business_id !== $business->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this service item.'
                ], 403);
            }

            // Check if the staff member belongs to the current business
            $staff = Staff::where('id', $request->staff_id)
                         ->where('business_id', $business->id)
                         ->first();
            
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected staff member does not belong to this business.'
                ], 400);
            }

            // Update the service item with the assigned staff
            $serviceItem->update([
                'staff_id' => $request->staff_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Staff assigned successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning staff: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the bulk create form for service bookings
     */
    public function bulkCreate()
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $services = Service::where('business_id', $business->id)->get();
        $staff = Staff::where('business_id', $business->id)->get();
        $customers = Customer::where('business_id', $business->id)->get();

        return view('service-bookings.bulk-create', compact('services', 'staff', 'customers'));
    }

    /**
     * Store multiple service bookings at once
     */
    public function bulkStore(Request $request)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return $this->redirectToBusinessSetup();
        }

        $request->validate([
            'service_date' => 'required|date',
            'bookings' => 'required|array|min:1',
            'bookings.*.customer_id' => 'nullable|exists:customers,id',
            'bookings.*.service_id' => 'required|exists:services,id',
            'bookings.*.staff_id' => 'required|exists:staff,id',
            'bookings.*.service_date' => 'required|date',
            'bookings.*.service_time' => 'nullable|date_format:H:i',
            'bookings.*.amount' => 'required|numeric|min:0',
            'bookings.*.payment_method' => 'required|string|in:cash,mpesa,card,bank_transfer,other',
            'bookings.*.payment_status' => 'required|string|in:pending,paid',
            'bookings.*.discount_type' => 'nullable|string|in:none,percentage,fixed',
            'bookings.*.discount_value' => 'nullable|numeric|min:0',
            'bookings.*.notes' => 'nullable|string|max:1000',
        ]);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::transaction(function () use ($request, $business, &$successCount, &$errorCount, &$errors) {
            foreach ($request->input('bookings') as $index => $bookingData) {
                try {
                    // Verify service belongs to business
                    $service = Service::where('id', $bookingData['service_id'])
                                    ->where('business_id', $business->id)
                                    ->first();
                    
                    if (!$service) {
                        throw new \Exception("Service does not belong to this business");
                    }

                    // Verify staff belongs to business
                    $staff = Staff::where('id', $bookingData['staff_id'])
                                 ->where('business_id', $business->id)
                                 ->first();
                    
                    if (!$staff) {
                        throw new \Exception("Staff member does not belong to this business");
                    }

                    // Calculate discount amounts (both percentage and fixed)
                    $discountType = $bookingData['discount_type'] ?? 'none';
                    $discountValue = $bookingData['discount_value'] ?? 0;
                    $subtotal = $bookingData['amount'];
                    
                    $discountAmount = 0;
                    if ($discountType === 'percentage' && $discountValue > 0) {
                        $discountAmount = ($subtotal * $discountValue) / 100;
                    } elseif ($discountType === 'fixed' && $discountValue > 0) {
                        $discountAmount = min($discountValue, $subtotal); // Don't exceed subtotal
                    }
                    
                    $finalAmount = max(0, $subtotal - $discountAmount);

                    // Combine date and time (use provided time or default to 09:00)
                    $serviceTime = $bookingData['service_time'] ?? '09:00';
                    $serviceDateTime = $bookingData['service_date'] . ' ' . $serviceTime;

                    // Create the service booking
                    $serviceBooking = ServiceBooking::create([
                        'business_id' => $business->id,
                        'customer_id' => $bookingData['customer_id'],
                        'service_date' => $serviceDateTime,
                        'total_amount' => $bookingData['amount'],
                        'discount_type' => $discountType,
                        'discount_value' => $discountValue > 0 ? $discountValue : null,
                        'discount_amount' => $discountAmount > 0 ? $discountAmount : null,
                        'subtotal' => $subtotal,
                        'final_amount' => $finalAmount,
                        'payment_status' => $bookingData['payment_status'],
                        'payment_method' => $bookingData['payment_method'],
                        'notes' => $bookingData['notes'],
                    ]);

                    // Create the service item
                    ServiceItem::create([
                        'service_booking_id' => $serviceBooking->id,
                        'service_id' => $service->id,
                        'staff_id' => $staff->id,
                        'amount' => $bookingData['amount'],
                        'commission_rate' => $service->commission_rate,
                        'commission_amount' => ($bookingData['amount'] * $service->commission_rate) / 100,
                        'sequence_order' => 1,
                        'notes' => $bookingData['notes'],
                    ]);

                    // Send notifications
                    try {
                        $notificationService = new NotificationService();
                        $notificationService->notifyNewServiceBooking($serviceBooking);
                    } catch (\Exception $e) {
                        // Log error but don't fail the booking creation
                        \Log::error('Failed to send service booking notifications: ' . $e->getMessage());
                    }

                    $successCount++;

                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }
        });

        // Build success message
        $serviceDate = date('M d, Y', strtotime($request->input('service_date')));
        $message = "Bulk service entry completed for {$serviceDate}. {$successCount} service" . ($successCount === 1 ? '' : 's') . " created successfully";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} failed.";
        } else {
            $message .= ".";
        }

        if ($errorCount > 0 && count($errors) > 0) {
            return redirect()->route('service-bookings.bulk-create')
                ->with('warning', $message)
                ->with('errors', $errors)
                ->withInput();
        }

        return redirect()->route('service-bookings.index')
            ->with('success', $message);
    }

    /**
     * Generate daily sales and expenses report
     */
    public function dailyReport(Request $request)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return response()->json(['success' => false, 'message' => 'Business not found'], 404);
        }

        $date = $request->input('date', now()->format('Y-m-d'));
        
        try {
            $reportDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Invalid date format'], 400);
        }

        // Get sales data for the day
        $salesData = $this->getSalesData($business->id, $reportDate);
        
        // Get expenses data for the day
        $expensesData = $this->getExpensesData($business->id, $reportDate);

        return response()->json([
            'success' => true,
            'data' => [
                'report_date' => $reportDate->format('Y-m-d'),
                'sales' => $salesData,
                'expenses' => $expensesData
            ]
        ]);
    }

    /**
     * Get sales data for a specific date
     */
    private function getSalesData($businessId, Carbon $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get service bookings for the day
        $bookings = ServiceBooking::where('business_id', $businessId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->with(['serviceItems.service', 'serviceItems.staff'])
            ->get();

        // Calculate totals
        $totalAmount = $bookings->sum('final_amount');
        $count = $bookings->count();
        $commissionTotal = $bookings->flatMap->serviceItems->sum('commission_amount');

        // Payment methods breakdown
        $paymentMethods = $bookings->groupBy('payment_method')
            ->map(function ($bookings, $method) {
                return [
                    'method' => ucfirst(str_replace('_', ' ', $method)),
                    'amount' => $bookings->sum('final_amount'),
                    'count' => $bookings->count()
                ];
            })->values();

        // Top services
        $topServices = $bookings->flatMap->serviceItems
            ->groupBy('service_id')
            ->map(function ($items, $serviceId) {
                $service = $items->first()->service;
                return [
                    'name' => $service ? $service->name : 'Unknown Service',
                    'count' => $items->count(),
                    'total' => $items->sum('amount')
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        return [
            'count' => $count,
            'total_amount' => $totalAmount,
            'commission_total' => $commissionTotal,
            'payment_methods' => $paymentMethods,
            'top_services' => $topServices
        ];
    }

    /**
     * Get expenses data for a specific date
     */
    private function getExpensesData($businessId, Carbon $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        // Get costs for the day
        $costs = Cost::where('business_id', $businessId)
            ->whereDate('date', $date->format('Y-m-d'))
            ->get();

        // Calculate totals
        $totalAmount = $costs->sum('amount');
        $count = $costs->count();

        // Expenses by type
        $byType = $costs->groupBy('type')
            ->map(function ($costs, $type) {
                return [
                    'type' => ucfirst(str_replace('_', ' ', $type)),
                    'amount' => $costs->sum('amount'),
                    'count' => $costs->count()
                ];
            })->values();

        return [
            'count' => $count,
            'total_amount' => $totalAmount,
            'by_type' => $byType
        ];
    }

    /**
     * Export daily report as PDF (simplified version)
     */
    public function exportPDF(Request $request)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return redirect()->back()->with('error', 'Business not found');
        }

        $date = $request->input('date', now()->format('Y-m-d'));
        $reportDate = Carbon::parse($date);

        $salesData = $this->getSalesData($business->id, $reportDate);
        $expensesData = $this->getExpensesData($business->id, $reportDate);

        $data = [
            'business' => $business,
            'report_date' => $reportDate,
            'sales' => $salesData,
            'expenses' => $expensesData,
            'net_amount' => $salesData['total_amount'] - $expensesData['total_amount']
        ];

        // For now, return a simple HTML view that can be printed to PDF by the browser
        return view('reports.daily-report-print', $data);
    }

    /**
     * Export daily report as Excel (simplified CSV version)
     */
    public function exportExcel(Request $request)
    {
        $business = $this->currentBusiness();
        if (!$business) {
            return redirect()->back()->with('error', 'Business not found');
        }

        $date = $request->input('date', now()->format('Y-m-d'));
        $reportDate = Carbon::parse($date);

        $salesData = $this->getSalesData($business->id, $reportDate);
        $expensesData = $this->getExpensesData($business->id, $reportDate);

        // Generate CSV content
        $csvContent = $this->generateReportCSV($business, $reportDate, $salesData, $expensesData);
        
        $filename = 'daily-report-' . $reportDate->format('Y-m-d') . '.csv';
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate CSV content for the report
     */
    private function generateReportCSV($business, $reportDate, $salesData, $expensesData)
    {
        $csv = [];
        $csv[] = ['Daily Sales & Expense Report'];
        $csv[] = ['Business:', $business->name];
        $csv[] = ['Date:', $reportDate->format('Y-m-d')];
        $csv[] = ['Generated:', now()->format('Y-m-d H:i:s')];
        $csv[] = [];
        
        // Sales Summary
        $csv[] = ['SALES SUMMARY'];
        $csv[] = ['Total Services:', $salesData['count']];
        $csv[] = ['Total Revenue:', 'KSh ' . number_format($salesData['total_amount'], 2)];
        $csv[] = ['Total Commission:', 'KSh ' . number_format($salesData['commission_total'], 2)];
        $csv[] = [];
        
        // Payment Methods
        $csv[] = ['Payment Methods'];
        foreach ($salesData['payment_methods'] as $method) {
            $csv[] = [$method['method'], 'KSh ' . number_format($method['amount'], 2)];
        }
        $csv[] = [];
        
        // Top Services
        $csv[] = ['Top Services'];
        foreach ($salesData['top_services'] as $service) {
            $csv[] = [$service['name'], $service['count'] . 'x', 'KSh ' . number_format($service['total'], 2)];
        }
        $csv[] = [];
        
        // Expenses Summary
        $csv[] = ['EXPENSES SUMMARY'];
        $csv[] = ['Total Expenses:', $expensesData['count']];
        $csv[] = ['Total Cost:', 'KSh ' . number_format($expensesData['total_amount'], 2)];
        $csv[] = [];
        
        // Expense Categories
        $csv[] = ['Expense Categories'];
        foreach ($expensesData['by_type'] as $type) {
            $csv[] = [$type['type'], 'KSh ' . number_format($type['amount'], 2)];
        }
        $csv[] = [];
        
        // Summary
        $netAmount = $salesData['total_amount'] - $expensesData['total_amount'];
        $csv[] = ['DAILY SUMMARY'];
        $csv[] = ['Total Sales:', 'KSh ' . number_format($salesData['total_amount'], 2)];
        $csv[] = ['Total Expenses:', 'KSh ' . number_format($expensesData['total_amount'], 2)];
        $csv[] = ['Net Amount:', 'KSh ' . number_format($netAmount, 2)];
        
        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }
}
