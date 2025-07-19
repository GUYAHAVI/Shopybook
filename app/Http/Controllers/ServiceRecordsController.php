<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\ServiceItem;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRecordsController extends Controller
{
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

    public function store(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_date' => 'required|date',
            'notes' => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.staff_id' => 'required|exists:staff,id',
            'services.*.sequence_order' => 'required|integer|min:1',
        ]);

        $serviceRecord = ServiceRecord::create([
            'business_id' => $business->id,
            'customer_id' => $validated['customer_id'],
            'service_date' => $validated['service_date'],
            'status' => 'in_progress',
            'notes' => $validated['notes'],
        ]);

        foreach ($validated['services'] as $serviceData) {
            $service = Service::find($serviceData['service_id']);
            
            ServiceItem::create([
                'service_record_id' => $serviceRecord->id,
                'service_id' => $serviceData['service_id'],
                'staff_id' => $serviceData['staff_id'],
                'sequence_order' => $serviceData['sequence_order'],
                'price' => $service->price,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('service-records.show', $serviceRecord)
            ->with('success', 'Service record created successfully!');
    }

    public function show(ServiceRecord $serviceRecord)
    {
        $this->authorize('view', $serviceRecord);
        
        $serviceRecord->load(['customer', 'serviceItems.service', 'serviceItems.staff']);
        
        return view('service-records.show', compact('serviceRecord'));
    }

    public function edit(ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);
        
        $business = Auth::user()->business;
        $services = Service::where('business_id', $business->id)->get();
        $staff = Staff::where('business_id', $business->id)->get();
        $customers = Customer::where('business_id', $business->id)->get();
        
        $serviceRecord->load(['serviceItems']);
        
        return view('service-records.edit', compact('serviceRecord', 'services', 'staff', 'customers'));
    }

    public function update(Request $request, ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'service_date' => 'required|date',
            'notes' => 'nullable|string',
            'status' => 'required|in:in_progress,completed,cancelled',
        ]);

        $serviceRecord->update($validated);

        return redirect()->route('service-records.show', $serviceRecord)
            ->with('success', 'Service record updated successfully!');
    }

    public function destroy(ServiceRecord $serviceRecord)
    {
        $this->authorize('delete', $serviceRecord);
        
        $serviceRecord->delete();

        return redirect()->route('service-records.index')
            ->with('success', 'Service record deleted successfully!');
    }

    public function addService(Request $request, ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);

        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'sequence_order' => 'required|integer|min:1',
        ]);

        $service = Service::find($validated['service_id']);
        
        ServiceItem::create([
            'service_record_id' => $serviceRecord->id,
            'service_id' => $validated['service_id'],
            'staff_id' => $validated['staff_id'],
            'sequence_order' => $validated['sequence_order'],
            'price' => $service->price,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Service added to record successfully!');
    }

    public function removeService(ServiceItem $serviceItem)
    {
        $this->authorize('update', $serviceItem->serviceRecord);
        
        $serviceItem->delete();

        return back()->with('success', 'Service removed from record successfully!');
    }

    public function complete(ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);
        
        $serviceRecord->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Mark all service items as completed
        $serviceRecord->serviceItems()->update(['status' => 'completed']);

        return back()->with('success', 'Service record marked as completed!');
    }
}
