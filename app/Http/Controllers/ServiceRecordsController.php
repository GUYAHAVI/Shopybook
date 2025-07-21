<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\ServiceItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceRecordsController extends Controller
{
    /**
     * Display a listing of service records
     */
    public function index()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $serviceRecords = ServiceRecord::where('business_id', $business->id)
            ->with(['customer', 'serviceItems.service', 'serviceItems.staff'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('service-records.index', compact('serviceRecords'));
    }

    /**
     * Show the form for creating a new service record
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

        return view('service-records.create', compact('services', 'staff', 'customers'));
    }

    /**
     * Store a newly created service record
     */
    public function store(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'customer_id' => 'nullable|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'performed_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $business) {
            // Create the service record
            $serviceRecord = ServiceRecord::create([
                'business_id' => $business->id,
                'customer_id' => $request->input('customer_id'),
                'service_date' => $request->input('performed_at'),
                'total_amount' => $request->input('amount'),
                'payment_status' => 'pending',
                'notes' => $request->input('notes'),
            ]);

            // Create service item
            ServiceItem::create([
                'service_record_id' => $serviceRecord->id,
                'service_id' => $request->input('service_id'),
                'staff_id' => $request->input('staff_id'),
                'amount' => $request->input('amount'),
                'sequence_order' => 1,
                'notes' => $request->input('notes'),
            ]);
        });

        return redirect()->route('service-records.index')
            ->with('success', 'Service record created successfully.');
    }

    /**
     * Display the specified service record
     */
    public function show(ServiceRecord $serviceRecord)
    {
        $this->authorize('view', $serviceRecord);
        $serviceRecord->load(['customer', 'serviceItems.service', 'serviceItems.staff']);
        
        return view('service-records.show', compact('serviceRecord'));
    }

    /**
     * Show the form for editing the specified service record
     */
    public function edit(ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);
        
        $business = Auth::user()->business;
        $services = Service::where('business_id', $business->id)->get();
        $staff = Staff::where('business_id', $business->id)->get();
        $customers = Customer::where('business_id', $business->id)->get();

        $serviceRecord->load(['serviceItems.service', 'serviceItems.staff']);

        return view('service-records.edit', compact('serviceRecord', 'services', 'staff', 'customers'));
    }

    /**
     * Update the specified service record
     */
    public function update(Request $request, ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'customer_id' => 'nullable|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'performed_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $serviceRecord) {
            // Update the service record
            $serviceRecord->update([
                'customer_id' => $request->input('customer_id'),
                'service_date' => $request->input('performed_at'),
                'total_amount' => $request->input('amount'),
                'notes' => $request->input('notes'),
            ]);

            // Delete existing service items and create new one
            $serviceRecord->serviceItems()->delete();

            ServiceItem::create([
                'service_record_id' => $serviceRecord->id,
                'service_id' => $request->input('service_id'),
                'staff_id' => $request->input('staff_id'),
                'amount' => $request->input('amount'),
                'sequence_order' => 1,
                'notes' => $request->input('notes'),
            ]);
        });

        return redirect()->route('service-records.index')
            ->with('success', 'Service record updated successfully.');
    }

    /**
     * Remove the specified service record
     */
    public function destroy(ServiceRecord $serviceRecord)
    {
        $this->authorize('delete', $serviceRecord);

        DB::transaction(function () use ($serviceRecord) {
            $serviceRecord->serviceItems()->delete();
            $serviceRecord->delete();
        });

        return redirect()->route('service-records.index')
            ->with('success', 'Service record deleted successfully.');
    }

    /**
     * Add a service to an existing service record
     */
    public function addService(Request $request, ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Get next sequence order
        $nextOrder = $serviceRecord->serviceItems()->max('sequence_order') + 1;

        ServiceItem::create([
            'service_record_id' => $serviceRecord->id,
            'service_id' => $request->service_id,
            'staff_id' => $request->staff_id,
            'amount' => $request->amount,
            'sequence_order' => $nextOrder,
            'notes' => $request->notes,
        ]);

        // Update total amount
        $totalAmount = $serviceRecord->serviceItems()->sum('amount');
        $serviceRecord->update(['total_amount' => $totalAmount]);

        return redirect()->route('service-records.show', $serviceRecord)
            ->with('success', 'Service added successfully.');
    }

    /**
     * Remove a service item from a service record
     */
    public function removeService(ServiceItem $serviceItem)
    {
        $serviceRecord = $serviceItem->serviceRecord;
        $this->authorize('update', $serviceRecord);

        $serviceItem->delete();

        // Update total amount
        $totalAmount = $serviceRecord->serviceItems()->sum('amount');
        $serviceRecord->update(['total_amount' => $totalAmount]);

        return redirect()->route('service-records.show', $serviceRecord)
            ->with('success', 'Service removed successfully.');
    }

    /**
     * Mark a service record as complete
     */
    public function complete(ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);

        $serviceRecord->update([
            'payment_status' => 'completed',
        ]);

        return redirect()->route('service-records.show', $serviceRecord)
            ->with('success', 'Service record marked as complete.');
    }
}
