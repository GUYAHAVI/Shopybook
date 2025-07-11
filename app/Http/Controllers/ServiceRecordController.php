<?php

namespace App\Http\Controllers;

use App\Models\ServiceRecord;
use App\Models\Service;
use App\Models\Staff;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceRecordController extends Controller
{
    public function index()
    {
        $records = ServiceRecord::where('business_id', Auth::user()->business->id)->with(['service', 'staff', 'customer'])->orderByDesc('performed_at')->get();
        return view('service-records.index', compact('records'));
    }

    public function create()
    {
        $services = Service::where('business_id', Auth::user()->business->id)->get();
        $staff = Staff::where('business_id', Auth::user()->business->id)->get();
        $customers = Customer::where('business_id', Auth::user()->business->id)->get();
        return view('service-records.create', compact('services', 'staff', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'customer_id' => 'nullable|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'performed_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $data['business_id'] = Auth::user()->business->id;
        ServiceRecord::create($data);
        return redirect()->route('service-records.index')->with('success', 'Service record added successfully.');
    }

    public function show(ServiceRecord $serviceRecord)
    {
        $this->authorize('view', $serviceRecord);
        return view('service-records.show', compact('serviceRecord'));
    }

    public function edit(ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);
        $services = Service::where('business_id', Auth::user()->business->id)->get();
        $staff = Staff::where('business_id', Auth::user()->business->id)->get();
        $customers = Customer::where('business_id', Auth::user()->business->id)->get();
        return view('service-records.edit', compact('serviceRecord', 'services', 'staff', 'customers'));
    }

    public function update(Request $request, ServiceRecord $serviceRecord)
    {
        $this->authorize('update', $serviceRecord);
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'staff_id' => 'required|exists:staff,id',
            'customer_id' => 'nullable|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'commission' => 'required|numeric|min:0',
            'performed_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        $serviceRecord->update($data);
        return redirect()->route('service-records.index')->with('success', 'Service record updated successfully.');
    }

    public function destroy(ServiceRecord $serviceRecord)
    {
        $this->authorize('delete', $serviceRecord);
        $serviceRecord->delete();
        return redirect()->route('service-records.index')->with('success', 'Service record deleted successfully.');
    }
} 