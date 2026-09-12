<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ReengagementEmail;
use App\Models\PageVisit;
use App\Models\User;
use App\Services\ReengagementEmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReengagementController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $days = in_array($days, [7, 14, 30, 60, 90, 180]) ? $days : 30;
        $cutoff = Carbon::now()->subDays($days);

        // Users who have been inactive for the given period (not admins, not current user)
        $users = User::where('is_admin', false)
            ->where('id', '!=', auth()->id())
            ->where(function ($q) use ($cutoff) {
                $q->whereDoesntHave('pageVisits', function ($q2) use ($cutoff) {
                    $q2->where('created_at', '>', $cutoff);
                })->orWhereDoesntHave('pageVisits');
            })
            ->with('business')
            ->paginate(25)
            ->withQueryString();

        // Add last-visit info to each user
        $users->getCollection()->transform(function ($user) {
            $lastVisit = PageVisit::where('user_id', $user->id)->latest()->first();
            $user->last_visit_at = $lastVisit?->created_at;
            $user->last_visit_page = $lastVisit?->route_name ?? $lastVisit?->path;
            $user->visit_count = PageVisit::where('user_id', $user->id)->count();
            return $user;
        });

        return view('admin.reengagement.index', compact('users', 'days'));
    }

    /**
     * Generate an AI-drafted email for a specific user (AJAX).
     */
    public function draft(Request $request, User $user)
    {
        $service = app(ReengagementEmailService::class);
        $draft = $service->draftEmail($user);

        return response()->json($draft);
    }

    /**
     * Send a re-engagement email to a specific user.
     */
    public function send(Request $request, User $user)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:200',
            'body'    => 'required|string|max:5000',
        ]);

        if ($user->is_admin || $user->id === auth()->id()) {
            return back()->with('error', 'You cannot send a re-engagement email to this user.');
        }

        try {
            Mail::to($user->email)->send(new ReengagementEmail($validated['subject'], $validated['body']));

            Log::info('Re-engagement email sent', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'admin_id' => auth()->id(),
                'subject' => $validated['subject'],
            ]);

            return back()->with('success', "Email sent to {$user->email}.");
        } catch (\Throwable $e) {
            Log::error('Re-engagement email failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Draft personalized emails for multiple users at once (AJAX).
     * Returns one draft per user.
     */
    public function draftBulk(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array|max:25',
            'user_ids.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])
            ->where('is_admin', false)
            ->where('id', '!=', auth()->id())
            ->with('business')
            ->get();

        $service = app(ReengagementEmailService::class);
        $drafts = [];

        foreach ($users as $user) {
            $draft = $service->draftEmail($user);
            $drafts[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'subject' => $draft['subject'],
                'body' => $draft['body'],
                'source' => $draft['source'],
            ];
        }

        return response()->json(['drafts' => $drafts]);
    }

    /**
     * Send personalized emails to multiple users — each with its own subject/body.
     */
    public function sendBulk(Request $request)
    {
        $validated = $request->validate([
            'emails' => 'required|array|max:25',
            'emails.*.user_id' => 'required|exists:users,id',
            'emails.*.subject' => 'required|string|max:200',
            'emails.*.body'    => 'required|string|max:5000',
        ]);

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($validated['emails'] as $item) {
            $user = User::find($item['user_id']);
            if (!$user || $user->is_admin || $user->id === auth()->id()) {
                $failed++;
                continue;
            }

            try {
                Mail::to($user->email)->send(new ReengagementEmail($item['subject'], $item['body']));
                $sent++;
            } catch (\Throwable $e) {
                Log::error("Bulk re-engagement failed for {$user->email}: " . $e->getMessage());
                $failed++;
                $errors[] = "{$user->email}: " . $e->getMessage();
            }
        }

        $msg = "Sent {$sent} personalized email(s).";
        if ($failed > 0) $msg .= " {$failed} failed.";
        return back()->with('success', $msg)->with('bulk-errors', $errors);
    }
}
