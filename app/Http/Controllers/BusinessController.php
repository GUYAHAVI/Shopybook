<?php
// app/Http/Controllers/BusinessController.php
namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    public function create()
    {
        if (auth()->user()->business) {
            return redirect()->route('dashboard');
        }
        return view('business.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:businesses,email',
            'phone' => 'required|string|max:20',
            'business_type' => 'required|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $business = tenancy()->create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'business_type' => $validated['business_type'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('logo')) {
            $business->update([
                'logo_path' => $request->file('logo')->store('business-logos')
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Business profile created successfully!');
    }

    public function edit(Business $business)
    {
        $this->authorize('update', $business);
        return view('business.edit', compact('business'));
    }

    public function update(Request $request, Business $business)
    {
        $this->authorize('update', $business);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable','email', Rule::unique('businesses')->ignore($business->id)],
            'phone' => 'required|string|max:20',
            'business_type' => 'required|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $business->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'business_type' => $validated['business_type'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('logo')) {
            Storage::delete($business->logo_path);
            $business->update([
                'logo_path' => $request->file('logo')->store('business-logos')
            ]);
        }

        return back()->with('success', 'Business profile updated successfully!');
    }

    public function destroy(Business $business)
    {
        $this->authorize('delete', $business);
        
        // Delete tenant resources
        tenancy()->delete($business);
        
        return redirect()->route('dashboard')->with('success', 'Business deleted successfully');
    }
}