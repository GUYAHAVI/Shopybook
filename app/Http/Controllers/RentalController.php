<?php

namespace App\Http\Controllers;

use App\Models\RentalPayment;
use App\Models\RentalProperty;
use App\Models\RentalTenant;
use App\Models\RentalUnit;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RentalController extends Controller
{
    // ── Units ────────────────────────────────────────────────────────────────

    public function units(Request $request)
    {
        $business = auth()->user()->business;

        $query = RentalUnit::where('business_id', $business->id)
            ->with(['property', 'tenant']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->boolean('listed')) {
            $query->where('is_listed', true)->where('status', 'vacant');
        }

        $units       = $query->orderBy('label')->paginate(24)->withQueryString();
        $properties  = $business->rentalProperties()->orderBy('name')->get();
        $orphanCount = RentalUnit::where('business_id', $business->id)
            ->whereNull('rental_property_id')->count();

        return view('rentals.units.index', compact('units', 'properties', 'orphanCount'));
    }

    public function createUnit()
    {
        $business   = auth()->user()->business;
        $properties = $business->rentalProperties()->orderBy('name')->get();

        return view('rentals.units.create', compact('properties'));
    }

    public function storeUnit(Request $request)
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'rental_property_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('rental_properties', 'id')
                    ->where('business_id', $business->id),
            ],
            'label_prefix'       => 'nullable|string|max:60',
            'count'              => 'required|integer|min:1|max:200',
            'rent_amount'        => 'required|numeric|min:1',
            'bedrooms'           => 'nullable|integer|min:0|max:20',
            'unit_type'          => 'nullable|string|max:30',
            'description'        => 'nullable|string|max:2000',
            'images'             => 'nullable|array|max:6',
            'images.*'           => 'image|max:4096',
        ]);

        // Shared images apply to every unit created in this batch
        $imagePaths = $this->storeImages($request, $business->id);

        $prefix = trim($validated['label_prefix'] ?? '') ?: 'Unit';
        $count  = (int) $validated['count'];

        DB::transaction(function () use ($validated, $business, $prefix, $count, $imagePaths) {
            $start = RentalUnit::where('business_id', $business->id)->count() + 1;

            for ($i = 0; $i < $count; $i++) {
                RentalUnit::create([
                    'business_id'         => $business->id,
                    'rental_property_id'  => $validated['rental_property_id'] ?? null,
                    'label'               => $count === 1 ? $prefix : "{$prefix} " . ($start + $i),
                    'rent_amount'         => $validated['rent_amount'],
                    'bedrooms'            => $validated['bedrooms'] ?? null,
                    'unit_type'           => $validated['unit_type'] ?? null,
                    'description'         => $validated['description'] ?? null,
                    'images'              => $imagePaths ?: null,
                    'status'              => 'vacant',
                    'is_listed'           => false,
                ]);
            }
        });

        return redirect()->route('rentals.units.index')
            ->with('success', $count === 1 ? 'Unit created.' : "{$count} units created.");
    }

    /** AI-enhance a unit description (Claude). */
    public function enhanceDescription(Request $request)
    {
        $request->validate([
            'description' => 'nullable|string|max:2000',
            'label'       => 'nullable|string|max:80',
            'unit_type'   => 'nullable|string|max:30',
            'bedrooms'    => 'nullable|integer|min:0|max:20',
            'rent_amount' => 'nullable|numeric',
            'location'    => 'nullable|string|max:150',
        ]);

        $apiKey = config('services.claude.api_key');
        if (empty($apiKey)) {
            return response()->json(['error' => 'AI is not configured on this server.'], 422);
        }

        $notes    = trim((string) $request->input('description'));
        $bedrooms = $request->input('bedrooms');
        $context  = collect([
            $request->input('label'),
            $request->input('unit_type'),
            $bedrooms !== null && $bedrooms !== '' ? ((int) $bedrooms === 0 ? 'bedsitter' : "{$bedrooms} bedroom(s)") : null,
            $request->input('rent_amount') ? 'KES ' . number_format((float) $request->input('rent_amount')) . '/month' : null,
            $request->input('location'),
        ])->filter()->implode(' · ');

        $prompt = "You write short, appealing rental listing descriptions for Kenyan house-seekers.\n"
            . "Unit details: " . ($context ?: 'rental unit') . "\n"
            . ($notes !== '' ? "Owner's rough notes: \"{$notes}\"\n" : "No notes provided — invent a clean, honest description.\n")
            . "Write ONE plain paragraph (max 45 words). Mention location/features if given, don't invent prices or amenities not listed. "
            . "No markdown, no quotes — return only the description text.";

        try {
            $resp = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])->timeout(20)->post('https://api.anthropic.com/v1/messages', [
                'model'      => config('services.claude.model', 'claude-haiku-4-5-20251001'),
                'max_tokens' => 300,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            if (!$resp->successful()) {
                return response()->json(['error' => 'AI service unavailable right now — try again.'], 502);
            }

            $text = trim((string) $resp->json('content.0.text', ''));
            $text = trim($text, "\"' \n");
            if ($text === '') {
                return response()->json(['error' => 'AI returned an empty description.'], 502);
            }

            return response()->json(['description' => $text]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'AI request failed — check your connection.'], 502);
        }
    }

    /** Assign all unlinked units to a property (one-click cleanup). */
    public function assignProperty(Request $request)
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'rental_property_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('rental_properties', 'id')
                    ->where('business_id', $business->id),
            ],
        ]);

        $count = RentalUnit::where('business_id', $business->id)
            ->whereNull('rental_property_id')
            ->update(['rental_property_id' => $validated['rental_property_id']]);

        return back()->with('success', "{$count} unit(s) assigned — they now appear in location search.");
    }

    public function editUnit(RentalUnit $unit)
    {
        $this->authorizeRental($unit);
        $properties = auth()->user()->business->rentalProperties()->orderBy('name')->get();
        return view('rentals.units.edit', compact('unit', 'properties'));
    }

    public function updateUnit(Request $request, RentalUnit $unit)
    {
        $this->authorizeRental($unit);

        $validated = $request->validate([
            'rental_property_id' => [
                'required',
                \Illuminate\Validation\Rule::exists('rental_properties', 'id')
                    ->where('business_id', $business->id),
            ],
            'label'              => 'required|string|max:80',
            'rent_amount'        => 'required|numeric|min:1',
            'bedrooms'           => 'nullable|integer|min:0|max:20',
            'unit_type'          => 'nullable|string|max:30',
            'description'        => 'nullable|string|max:2000',
            'status'             => 'required|in:vacant,occupied,maintenance',
            'images'             => 'nullable|array|max:6',
            'images.*'           => 'image|max:4096',
            'remove_images'      => 'nullable|array',
        ]);

        $images = $unit->images ?? [];

        // Remove selected images
        foreach ($request->input('remove_images', []) as $path) {
            if (($k = array_search($path, $images)) !== false) {
                Storage::disk('public')->delete($path);
                unset($images[$k]);
            }
        }
        $images = array_values($images);

        // Append new uploads (cap at 6 total)
        foreach (array_slice($this->storeImages($request, $unit->business_id), 0, 6 - count($images)) as $p) {
            $images[] = $p;
        }

        $unit->update([
            'rental_property_id' => $validated['rental_property_id'] ?? null,
            'label'              => $validated['label'],
            'rent_amount'        => $validated['rent_amount'],
            'bedrooms'           => $validated['bedrooms'] ?? null,
            'unit_type'          => $validated['unit_type'] ?? null,
            'description'        => $validated['description'] ?? null,
            'status'             => $validated['status'],
            'images'             => $images ?: null,
        ]);

        return redirect()->route('rentals.units.index')->with('success', 'Unit updated.');
    }

    public function toggleListing(RentalUnit $unit)
    {
        $this->authorizeRental($unit);

        if (!$unit->is_listed && $unit->status !== 'vacant') {
            return back()->with('error', 'Only vacant units can be listed publicly.');
        }

        $unit->update(['is_listed' => !$unit->is_listed]);
        return back()->with('success', $unit->is_listed ? 'Unit is now publicly listed.' : 'Unit removed from public listings.');
    }

    public function destroyUnit(RentalUnit $unit)
    {
        $this->authorizeRental($unit);

        if ($unit->status === 'occupied') {
            return back()->with('error', 'Vacate the tenant before deleting this unit.');
        }

        foreach ($unit->images ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        $unit->delete();

        return back()->with('success', 'Unit deleted.');
    }

    // ── Properties ───────────────────────────────────────────────────────────

    public function properties()
    {
        $business   = auth()->user()->business;
        $properties = $business->rentalProperties()->withCount('units')->orderBy('name')->get();
        return view('rentals.properties.index', compact('properties'));
    }

    public function storeProperty(Request $request)
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'name'        => 'required|string|max:120',
            'town'        => 'nullable|string|max:80',
            'estate'      => 'nullable|string|max:80',
            'description' => 'nullable|string|max:1000',
        ]);

        $business->rentalProperties()->create($validated);

        return back()->with('success', 'Property added.');
    }

    public function destroyProperty(RentalProperty $property)
    {
        $this->authorizeRental($property);
        $property->delete(); // units keep existing, property_id → null
        return back()->with('success', 'Property removed. Its units were kept.');
    }

    // ── Tenants ──────────────────────────────────────────────────────────────

    public function tenants(Request $request)
    {
        $business = auth()->user()->business;

        $query = RentalTenant::where('business_id', $business->id)->with('unit.property');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('phone', 'like', "%{$request->search}%"));
        }

        $tenants = $query->latest()->paginate(20)->withQueryString();
        $vacantUnits = RentalUnit::where('business_id', $business->id)
            ->where('status', 'vacant')->orderBy('label')->get();

        return view('rentals.tenants.index', compact('tenants', 'vacantUnits'));
    }

    public function storeTenant(Request $request)
    {
        $business = auth()->user()->business;

        $validated = $request->validate([
            'name'           => 'required|string|max:120',
            'phone'          => 'required|string|max:20',
            'email'          => 'nullable|email|max:191',
            'id_number'      => 'nullable|string|max:30',
            'rental_unit_id' => 'nullable|exists:rental_units,id',
            'rent_amount'    => 'nullable|numeric|min:1',
            'move_in_date'   => 'nullable|date',
            'notes'          => 'nullable|string|max:2000',
            'rent_paid'      => 'nullable|boolean',
            'paid_method'    => 'nullable|in:cash,mpesa,bank,other',
        ]);

        DB::transaction(function () use ($validated, $business) {
            $unit = null;
            if (!empty($validated['rental_unit_id'])) {
                $unit = RentalUnit::where('business_id', $business->id)
                    ->where('id', $validated['rental_unit_id'])
                    ->where('status', 'vacant')
                    ->firstOrFail();
            }

            $tenant = RentalTenant::create([
                'business_id'    => $business->id,
                'rental_unit_id' => $unit?->id,
                'name'           => $validated['name'],
                'phone'          => $validated['phone'],
                'email'          => $validated['email'] ?? null,
                'id_number'      => $validated['id_number'] ?? null,
                'rent_amount'    => $validated['rent_amount'] ?? $unit?->rent_amount ?? 0,
                'status'         => 'active',
                'move_in_date'   => $validated['move_in_date'] ?? now(),
                'notes'          => $validated['notes'] ?? null,
            ]);

            $unit?->update(['status' => 'occupied', 'is_listed' => false]);

            if (!empty($validated['rent_paid']) && $tenant->rent_amount > 0) {
                RentalPayment::create([
                    'business_id'       => $business->id,
                    'rental_tenant_id'  => $tenant->id,
                    'rental_unit_id'    => $unit?->id,
                    'period'            => now()->format('Y-m'),
                    'amount'            => $tenant->rent_amount,
                    'method'            => $validated['paid_method'] ?? 'cash',
                    'status'            => 'paid',
                    'paid_at'           => now(),
                    'notes'             => 'Recorded at tenant registration',
                ]);
            }
        });

        return back()->with('success', 'Tenant recorded' . (!empty($validated['rental_unit_id']) ? ' and unit marked occupied.' : '.'));
    }

    public function vacateTenant(RentalTenant $tenant)
    {
        $this->authorizeRental($tenant);

        DB::transaction(function () use ($tenant) {
            $tenant->update(['status' => 'vacated', 'move_out_date' => now()]);
            $tenant->unit?->update(['status' => 'vacant']);
        });

        return back()->with('success', "{$tenant->name} marked as moved out. Unit is now vacant.");
    }

    // ── Rent Payments ────────────────────────────────────────────────────────

    public function payments(Request $request)
    {
        $business = auth()->user()->business;
        $period   = $request->input('period', now()->format('Y-m'));

        $tenants = RentalTenant::where('business_id', $business->id)
            ->where('status', 'active')
            ->with(['unit', 'payments' => fn($q) => $q->where('period', $period)->latest()])
            ->orderBy('name')
            ->get()
            ->map(function ($t) use ($period) {
                $t->periodPayment = $t->payments->firstWhere('status', 'paid')
                    ?? $t->payments->first();
                $t->periodPaid    = (float) $t->payments->where('status', 'paid')->sum('amount');
                $t->balance       = max(0, (float) $t->rent_amount - $t->periodPaid);
                $t->periodStatus  = $t->periodPaid >= (float) $t->rent_amount ? 'paid'
                    : ($t->periodPaid > 0 ? 'partial'
                    : ($t->payments->where('status', 'pending')->isNotEmpty() ? 'pending' : 'owing'));
                return $t;
            });

        $history = RentalPayment::where('business_id', $business->id)
            ->with(['tenant', 'unit'])
            ->latest()
            ->take(25)
            ->get();

        $summary = [
            'expected'   => $tenants->sum(fn($t) => (float) $t->rent_amount),
            'collected'  => $tenants->sum(fn($t) => $t->periodPaid),
            'owing'      => $tenants->filter(fn($t) => $t->balance > 0)->count(),
            'outstanding' => $tenants->sum(fn($t) => $t->balance),
        ];

        return view('rentals.payments.index', compact('tenants', 'history', 'summary', 'period'));
    }

    public function recordPayment(Request $request, RentalTenant $tenant)
    {
        $this->authorizeRental($tenant);

        $validated = $request->validate([
            'period'                => 'required|regex:/^\d{4}-\d{2}$/',
            'amount'                => 'required|numeric|min:1',
            'method'                => 'required|in:cash,mpesa,bank_transfer,card,cheque',
            'transaction_reference' => 'nullable|string|max:120',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        RentalPayment::create([
            'business_id'           => $tenant->business_id,
            'rental_tenant_id'      => $tenant->id,
            'rental_unit_id'        => $tenant->rental_unit_id,
            'period'                => $validated['period'],
            'amount'                => $validated['amount'],
            'method'                => $validated['method'],
            'status'                => 'paid',
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'paid_at'               => now(),
            'received_by'           => auth()->id(),
            'notes'                 => $validated['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Rent payment recorded.']);
    }

    public function stkPayment(Request $request, RentalTenant $tenant, MpesaService $mpesa)
    {
        $this->authorizeRental($tenant);

        $validated = $request->validate([
            'period' => 'required|regex:/^\d{4}-\d{2}$/',
            'amount' => 'required|numeric|min:1',
            'phone'  => 'nullable|string|max:20',
        ]);

        $phone = $validated['phone'] ?? $tenant->phone;

        // Pending payment row — the STK callback flips it to paid
        $payment = RentalPayment::create([
            'business_id'      => $tenant->business_id,
            'rental_tenant_id' => $tenant->id,
            'rental_unit_id'   => $tenant->rental_unit_id,
            'period'           => $validated['period'],
            'amount'           => $validated['amount'],
            'method'           => 'mpesa',
            'status'           => 'pending',
            'received_by'      => auth()->id(),
        ]);

        $accountRef = trim(($tenant->unit?->label ?? 'Rent') . ' ' . $tenant->name);
        $accountRef = substr($accountRef, 0, 12); // Daraja AccountReference limit

        $result = $mpesa->stkPush(
            $tenant->business,
            $phone,
            (float) $validated['amount'],
            $accountRef,
            null,
            $payment->id
        );

        if (!($result['success'] ?? false)) {
            $payment->delete();
            return response()->json(['success' => false, 'message' => $result['error'] ?? 'Failed to send STK push.'], 422);
        }

        $stk = \App\Models\MpesaStkRequest::where('checkout_request_id', $result['reference'])->first();
        $payment->update(['mpesa_stk_request_id' => $stk?->id]);

        return response()->json([
            'success'   => true,
            'message'   => $result['message'],
            'reference' => $result['reference'],
        ]);
    }

    /** Printable rent receipt for a paid payment. */
    public function receipt(RentalPayment $payment)
    {
        $this->authorizeRental($payment);
        abort_unless($payment->status === 'paid', 404);

        $payment->load(['tenant.unit.property', 'unit.property', 'business']);

        return view('rentals.receipt', [
            'payment'  => $payment,
            'business' => $payment->business,
        ]);
    }

    // ── Public booking requests ─────────────────────────────────────────────

    public function bookings(Request $request)
    {
        $business = auth()->user()->business;

        $bookings = \App\Models\RentalBooking::where('business_id', $business->id)
            ->with('unit.property')
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('rentals.bookings.index', compact('bookings'));
    }

    public function updateBooking(\App\Models\RentalBooking $booking, Request $request)
    {
        $this->authorizeRental($booking);

        $validated = $request->validate([
            'status' => 'required|in:new,contacted,confirmed,declined',
        ]);

        $booking->update(['status' => $validated['status']]);

        return back()->with('success', "Booking from {$booking->name} marked {$validated['status']}.");
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeRental($model): void
    {
        abort_unless($model->business_id === auth()->user()->business->id, 403);
    }

    private function storeImages(Request $request, string $businessId): array
    {
        $paths = [];
        foreach ($request->file('images', []) as $file) {
            $paths[] = $file->store("rentals/{$businessId}", 'public');
        }
        return $paths;
    }
}
