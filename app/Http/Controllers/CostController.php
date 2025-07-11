<?php

namespace App\Http\Controllers;

use App\Models\Cost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CostController extends Controller
{
    public function index()
    {
        $costs = Cost::where('business_id', Auth::user()->business->id)->orderByDesc('date')->get();
        return view('costs.index', compact('costs'));
    }

    public function create()
    {
        return view('costs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:utility,product,rent,water,misc,activity,renovation,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);
        $data['business_id'] = Auth::user()->business->id;
        Cost::create($data);
        return redirect()->route('costs.index')->with('success', 'Cost recorded successfully.');
    }

    public function show(Cost $cost)
    {
        $this->authorize('view', $cost);
        return view('costs.show', compact('cost'));
    }

    public function edit(Cost $cost)
    {
        $this->authorize('update', $cost);
        return view('costs.edit', compact('cost'));
    }

    public function update(Request $request, Cost $cost)
    {
        $this->authorize('update', $cost);
        $data = $request->validate([
            'type' => 'required|in:utility,product,rent,water,misc,activity,renovation,other',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
        ]);
        $cost->update($data);
        return redirect()->route('costs.index')->with('success', 'Cost updated successfully.');
    }

    public function destroy(Cost $cost)
    {
        $this->authorize('delete', $cost);
        $cost->delete();
        return redirect()->route('costs.index')->with('success', 'Cost deleted successfully.');
    }
} 