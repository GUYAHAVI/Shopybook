<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class TestimonialController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    // ── PUBLIC: submit a Shopybook platform review ────────────────────────

    public function submitPlatform(Request $request)
    {
        $this->throttleSubmission($request->ip(), 'platform');

        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'role'   => 'nullable|string|max:100',
            'quote'  => 'required|string|min:20|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        Testimonial::create([
            'type'        => 'platform',
            'name'        => $data['name'],
            'role'        => $data['role'] ?? null,
            'quote'       => $data['quote'],
            'rating'      => $data['rating'],
            'status'      => 'pending',
            'is_approved' => false,
            'ip_address'  => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('testimonial_submitted', true);
    }

    // ── PUBLIC: submit a review for a specific business ───────────────────

    public function submitBusiness(Request $request, $subdomain)
    {
        $website = \App\Models\Website::where('subdomain', $subdomain)
            ->where('is_published', true)
            ->firstOrFail();

        $this->throttleSubmission($request->ip(), 'business-' . $website->business_id);

        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'role'   => 'nullable|string|max:100',
            'quote'  => 'required|string|min:20|max:1000',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $testimonial = Testimonial::create([
            'type'        => 'business',
            'business_id' => $website->business_id,
            'name'        => $data['name'],
            'role'        => $data['role'] ?? null,
            'quote'       => $data['quote'],
            'rating'      => $data['rating'],
            'status'      => 'pending',
            'is_approved' => false,
            'ip_address'  => $request->ip(),
        ]);

        // Notify the business owner (in-app + email)
        $this->notificationService->notifyNewTestimonial($testimonial, $website->business);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('testimonial_submitted', true);
    }

    // ── OWNER: testimonial management page ───────────────────────────────

    public function ownerIndex(Request $request)
    {
        $business = Auth::user()->business;
        abort_unless($business, 403);

        $filter = $request->get('filter', 'pending');

        $query = Testimonial::forBusiness($business->id)->latest();

        if ($filter === 'deleted') {
            $query = Testimonial::onlyTrashed()
                ->forBusiness($business->id)
                ->latest('deleted_at');
        } elseif (in_array($filter, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $filter);
        }

        $testimonials = $query->paginate(15)->withQueryString();

        $counts = [
            'pending'  => Testimonial::forBusiness($business->id)->pending()->count(),
            'approved' => Testimonial::forBusiness($business->id)->approved()->count(),
            'rejected' => Testimonial::forBusiness($business->id)->rejected()->count(),
            'deleted'  => Testimonial::onlyTrashed()->forBusiness($business->id)->count(),
        ];

        return view('testimonials.index', compact('testimonials', 'filter', 'counts'));
    }

    // ── OWNER: approve ────────────────────────────────────────────────────

    public function ownerApprove(Testimonial $testimonial)
    {
        $this->authorizeOwner($testimonial);
        $testimonial->approve();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'approved']);
        }
        return back()->with('success', 'Review approved and published on your website.');
    }

    // ── OWNER: reject ─────────────────────────────────────────────────────

    public function ownerReject(Testimonial $testimonial)
    {
        $this->authorizeOwner($testimonial);
        $testimonial->reject();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'status' => 'rejected']);
        }
        return back()->with('success', 'Review rejected and hidden from your website.');
    }

    // ── OWNER: soft delete ────────────────────────────────────────────────

    public function ownerDelete(Testimonial $testimonial)
    {
        $this->authorizeOwner($testimonial);
        $testimonial->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Review moved to trash. You can restore it within 30 days.');
    }

    // ── OWNER: restore soft-deleted ───────────────────────────────────────

    public function ownerRestore(int $id)
    {
        $business = Auth::user()->business;
        abort_unless($business, 403);

        $testimonial = Testimonial::onlyTrashed()
            ->where('business_id', $business->id)
            ->findOrFail($id);

        $testimonial->restore();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Review restored successfully.');
    }

    // ── OWNER: permanent delete ───────────────────────────────────────────

    public function ownerForceDelete(int $id)
    {
        $business = Auth::user()->business;
        abort_unless($business, 403);

        $testimonial = Testimonial::onlyTrashed()
            ->where('business_id', $business->id)
            ->findOrFail($id);

        $testimonial->forceDelete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Review permanently deleted.');
    }

    // ── ADMIN: list all pending testimonials ──────────────────────────────

    public function adminIndex()
    {
        $pending  = Testimonial::pending()->latest()->paginate(20, ['*'], 'pending');
        $approved = Testimonial::approved()->latest()->paginate(20, ['*'], 'approved');

        return view('admin.testimonials.index', compact('pending', 'approved'));
    }

    // ── ADMIN: approve ────────────────────────────────────────────────────

    public function approve(Testimonial $testimonial)
    {
        $testimonial->approve();
        return back()->with('success', 'Testimonial approved.');
    }

    // ── ADMIN: delete ─────────────────────────────────────────────────────

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted.');
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function authorizeOwner(Testimonial $testimonial): void
    {
        $business = Auth::user()->business;
        abort_unless($business && $testimonial->business_id === $business->id, 403);
    }

    private function throttleSubmission(string $ip, string $scope): void
    {
        $key = 'testimonial-' . $scope . '-' . $ip;

        if (RateLimiter::tooManyAttempts($key, 2)) {
            throw ValidationException::withMessages([
                'quote' => 'You have submitted too many reviews recently. Please try again later.',
            ]);
        }

        RateLimiter::hit($key, 3600);
    }
}

