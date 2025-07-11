<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('business_id', Auth::user()->business->id)->get();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);
        $data['business_id'] = Auth::user()->business->id;
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
        return view('services.edit', compact('service'));
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

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
} 