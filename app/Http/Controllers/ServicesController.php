<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicesController extends Controller
{
    public function index()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $services = Service::where('business_id', $business->id)
            ->orderBy('name')
            ->get();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        // Get existing services for bundling options
        $services = Service::where('business_id', $business->id)->get();

        return view('services.create', compact('services'));
    }

    public function store(Request $request)
    {
        $business = Auth::user()->business;
        if (!$business) {
            return redirect()->route('business.choose-type');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:1000',
            'is_bundle_trigger' => 'boolean',
            'bundled_services' => 'nullable|array',
            'bundled_services.*' => 'exists:services,id',
            'is_complimentary' => 'boolean',
            'parent_service_id' => 'nullable|exists:services,id',
        ]);

        Service::create([
            'business_id' => $business->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'duration' => $validated['duration'],
            'commission_rate' => $validated['commission_rate'],
            'description' => $validated['description'],
            'is_bundle_trigger' => $request->has('is_bundle_trigger'),
            'bundled_services' => $validated['bundled_services'] ?? null,
            'is_complimentary' => $request->has('is_complimentary'),
            'parent_service_id' => $validated['parent_service_id'] ?? null,
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Service created successfully!');
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully!');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        
        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully!');
    }
}
