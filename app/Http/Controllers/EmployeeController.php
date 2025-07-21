<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $business = Business::findOrFail($request->input('business_id'));

        $employees = Employee::where('business_id', $business->id)
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(15);

        return view('employees.index', compact('employees', 'business'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $business = Business::findOrFail($request->input('business_id'));

        return view('employees.create', compact('business'));
    }

        /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $business = Business::findOrFail($request->input('business_id'));

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Generate unique employee ID
        $employeeId = 'EMP' . str_pad(Employee::where('business_id', $business->id)->count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Convert is_active to status
        $status = ($validated['is_active'] ?? false) ? 'active' : 'inactive';
        
        try {
            $employee = Employee::create([
                'business_id' => $business->id,
                'employee_id' => $employeeId,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'position' => $validated['position'],
                'department' => $validated['department'],
                'hire_date' => $validated['hire_date'],
                'salary' => $validated['salary'],
                'employment_type' => 'full_time', // Default value
                'status' => $status,
                'address' => $validated['address'],
            ]);

            return redirect()->route('employees.index', ['business_id' => $business->id])
                ->with('success', 'Employee created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $business = Business::findOrFail($request->input('business_id'));
        $employee = Employee::where('business_id', $business->id)->findOrFail($id);
        
        return view('employees.show', compact('employee', 'business'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $business = Business::findOrFail($request->input('business_id'));
        $employee = Employee::where('business_id', $business->id)->findOrFail($id);
        
        return view('employees.edit', compact('employee', 'business'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $business = Business::findOrFail($request->input('business_id'));
        $employee = Employee::where('business_id', $business->id)->findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:employees,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Convert is_active to status
        $status = ($validated['is_active'] ?? false) ? 'active' : 'inactive';

        $employee->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'position' => $validated['position'],
            'department' => $validated['department'],
            'hire_date' => $validated['hire_date'],
            'salary' => $validated['salary'],
            'status' => $status,
            'address' => $validated['address'],
        ]);

        return redirect()->route('employees.index', ['business_id' => $business->id])
            ->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $business = Business::findOrFail($request->input('business_id'));
        $employee = Employee::where('business_id', $business->id)->findOrFail($id);
        
        $employee->delete();

        return redirect()->route('employees.index', ['business_id' => $business->id])
            ->with('success', 'Employee deleted successfully!');
    }
}
