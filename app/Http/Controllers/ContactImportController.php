<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use App\Models\ImportedContact;
use App\Imports\ContactsImport;
use App\Services\VCardImportService;
use App\Services\GoogleContactsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ContactImportController extends Controller
{
    /**
     * Display contact groups and import interface
     */
    public function index()
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $contactGroups = ContactGroup::where('business_id', $businessId)
            ->withCount('contacts')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('marketing.contacts.index', compact('contactGroups'));
    }

    /**
     * Show create contact group form
     */
    public function createGroup()
    {
        return view('marketing.contacts.create-group');
    }

    /**
     * Store new contact group
     */
    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:customers,staff,suppliers,custom',
        ]);

        $businessId = Auth::user()->currentBusiness->id;

        $group = ContactGroup::create([
            'business_id' => $businessId,
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'contact_count' => 0,
        ]);

        return redirect()
            ->route('contacts.show', $group->id)
            ->with('success', 'Contact group created successfully!');
    }

    /**
     * Show specific contact group
     */
    public function show($id)
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->with('contacts')
            ->findOrFail($id);

        return view('marketing.contacts.show', compact('group'));
    }

    /**
     * Show import interface for a group
     */
    public function showImport($id)
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->findOrFail($id);

        return view('marketing.contacts.import', compact('group'));
    }

    /**
     * Import contacts from CSV/Excel file
     */
    public function importCsv(Request $request, $groupId)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->findOrFail($groupId);

        try {
            $import = new ContactsImport($groupId, $businessId);
            Excel::import($import, $request->file('file'));

            $errors = $import->failures();
            $imported = $group->contacts()->where('source', 'csv')->count();

            if (count($errors) > 0) {
                return back()->with('warning', "Imported {$imported} contacts with " . count($errors) . " errors.");
            }

            return back()->with('success', "Successfully imported {$imported} contacts!");
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing contacts: ' . $e->getMessage());
        }
    }

    /**
     * Import contacts from VCF file
     */
    public function importVcf(Request $request, $groupId)
    {
        $request->validate([
            'file' => 'required|file|mimes:vcf|max:10240', // 10MB max
        ]);

        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->findOrFail($groupId);

        try {
            $vcardService = new VCardImportService();
            $result = $vcardService->import(
                $request->file('file')->getPathname(),
                $groupId,
                $businessId
            );

            if ($result['success']) {
                $message = "Successfully imported {$result['imported']} contacts!";
                if (count($result['errors']) > 0) {
                    $message .= " " . count($result['errors']) . " contacts had errors.";
                }
                return back()->with('success', $message);
            }

            return back()->with('error', 'Error importing contacts.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing contacts: ' . $e->getMessage());
        }
    }

    /**
     * Initiate Google Contacts OAuth
     */
    public function initiateGoogleImport($groupId)
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->findOrFail($groupId);

        // Store group ID in session for callback
        session(['google_import_group_id' => $groupId]);

        $googleService = new GoogleContactsService();
        $authUrl = $googleService->getAuthUrl();

        return redirect($authUrl);
    }

    /**
     * Handle Google OAuth callback and import contacts
     */
    public function handleGoogleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('contacts.index')
                ->with('error', 'Google authentication failed.');
        }

        $groupId = session('google_import_group_id');
        if (!$groupId) {
            return redirect()->route('contacts.index')
                ->with('error', 'Session expired. Please try again.');
        }

        $businessId = Auth::user()->currentBusiness->id;
        
        try {
            $googleService = new GoogleContactsService();
            $token = $googleService->authenticate($request->code);
            
            $result = $googleService->import($token, $groupId, $businessId);

            if ($result['success']) {
                session()->forget('google_import_group_id');
                return redirect()->route('contacts.show', $groupId)
                    ->with('success', "Successfully imported {$result['imported']} contacts from Google!");
            }

            return redirect()->route('contacts.show', $groupId)
                ->with('error', 'Error importing contacts: ' . ($result['error'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            return redirect()->route('contacts.show', $groupId)
                ->with('error', 'Error importing contacts: ' . $e->getMessage());
        }
    }

    /**
     * Delete a contact group
     */
    public function destroyGroup($id)
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->findOrFail($id);

        $group->delete();

        return redirect()->route('contacts.index')
            ->with('success', 'Contact group deleted successfully!');
    }

    /**
     * Delete a specific contact
     */
    public function destroyContact($groupId, $contactId)
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $contact = ImportedContact::where('business_id', $businessId)
            ->where('contact_group_id', $groupId)
            ->findOrFail($contactId);

        $contact->delete();

        return back()->with('success', 'Contact deleted successfully!');
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="contacts_template.csv"',
        ];

        $columns = ['name', 'phone', 'email', 'company', 'position', 'address', 'notes'];
        
        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Add example rows
            fputcsv($file, ['John Doe', '254712345678', 'john@example.com', 'Acme Corp', 'Manager', '123 Main St', 'VIP customer']);
            fputcsv($file, ['Jane Smith', '0722345678', 'jane@example.com', 'Tech Ltd', 'Developer', '', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Sync existing customers to a contact group
     */
    public function syncCustomers($groupId)
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->findOrFail($groupId);

        // Get all customers for this business
        $customers = \App\Models\Customer::where('business_id', $businessId)->get();

        $imported = 0;
        foreach ($customers as $customer) {
            // Check if already imported
            $exists = ImportedContact::where('business_id', $businessId)
                ->where('contact_group_id', $groupId)
                ->where('phone', $customer->phone)
                ->exists();

            if (!$exists) {
                ImportedContact::create([
                    'business_id' => $businessId,
                    'contact_group_id' => $groupId,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'source' => 'manual',
                    'metadata' => [
                        'synced_from' => 'customers',
                        'customer_id' => $customer->id,
                    ],
                ]);
                $imported++;
            }
        }

        return back()->with('success', "Synced {$imported} customers to this group!");
    }

    /**
     * Sync existing employees to a contact group
     */
    public function syncEmployees($groupId)
    {
        $businessId = Auth::user()->currentBusiness->id;
        
        $group = ContactGroup::where('business_id', $businessId)
            ->findOrFail($groupId);

        // Get all employees for this business
        $employees = \App\Models\Employee::where('business_id', $businessId)->get();

        $imported = 0;
        foreach ($employees as $employee) {
            // Check if already imported
            $exists = ImportedContact::where('business_id', $businessId)
                ->where('contact_group_id', $groupId)
                ->where('phone', $employee->phone)
                ->exists();

            if (!$exists) {
                ImportedContact::create([
                    'business_id' => $businessId,
                    'contact_group_id' => $groupId,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone' => $employee->phone,
                    'position' => $employee->role,
                    'source' => 'manual',
                    'metadata' => [
                        'synced_from' => 'employees',
                        'employee_id' => $employee->id,
                    ],
                ]);
                $imported++;
            }
        }

        return back()->with('success', "Synced {$imported} employees to this group!");
    }
}



