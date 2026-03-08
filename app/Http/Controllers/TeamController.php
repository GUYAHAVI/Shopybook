<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\BusinessMember;
use App\Models\ActivityLog;
use App\Models\User;

class TeamController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        $business = auth()->user()->business;

        $members = BusinessMember::forBusiness($business->id)
            ->with('user', 'invitedBy')
            ->orderByRaw("FIELD(status, 'active', 'pending', 'suspended')")
            ->orderBy('created_at', 'desc')
            ->get();

        $logs = ActivityLog::forBusiness($business->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        $modules  = config('rbac.modules', []);
        $roles    = config('rbac.role_labels', []);
        $defaults = config('rbac.role_defaults', []);
        $isOwner  = $business->user_id === auth()->id();

        // Only the owner can access this page
        if (!$isOwner) {
            // Check if they have the 'team' permission
            if (!auth()->user()->hasModulePermission('team')) {
                return redirect()->route('dashboard')
                    ->with('error', 'Only the business owner can manage the team.');
            }
        }

        ActivityLog::record($business->id, 'team', 'viewed', 'Viewed team management page');

        return view('team.index', compact('business', 'members', 'logs', 'modules', 'roles', 'defaults', 'isOwner'));
    }

    // ─── Store (invite / add member) ─────────────────────────────────────────

    public function store(Request $request)
    {
        $business = auth()->user()->business;
        $this->authorizeOwner($business);

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|max:191',
            'role'        => 'required|in:admin,manager,cashier,staff,viewer',
            'password'    => 'nullable|string|min:6|max:72',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(config('rbac.modules', []))),
        ]);

        // Prevent duplicate invitations
        $existing = BusinessMember::where('business_id', $business->id)
            ->where('invited_email', $validated['email'])
            ->first();

        if ($existing) {
            return back()->with('error', 'A team member with that email already exists for this business.');
        }

        // Resolve or create the user account
        $user        = User::where('email', $validated['email'])->first();
        $tempPassword = null;
        $isNewUser    = false;

        if (!$user) {
            // Use the owner-supplied password, or generate one
            $tempPassword = $validated['password'] ?? Str::random(10);
            $user = User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($tempPassword),
                'email_verified_at' => now(), // auto-verify — business is already verified
            ]);
            $isNewUser = true;
        } else {
            // Existing user — auto-verify them (business is verified, no need for email check)
            // and optionally update their password if the owner set one
            $updates = [];
            if (!$user->email_verified_at) {
                $updates['email_verified_at'] = now();
            }
            if (!empty($validated['password'])) {
                $updates['password'] = Hash::make($validated['password']);
                $tempPassword = $validated['password'];
            }
            if ($updates) {
                $user->update($updates);
            }
        }

        // Determine permissions
        $permissions = $validated['permissions']
            ?? config("rbac.role_defaults.{$validated['role']}", []);

        $member = BusinessMember::create([
            'business_id'   => $business->id,
            'user_id'       => $user->id,
            'invited_email' => $validated['email'],
            'name'          => $validated['name'],
            'role'          => $validated['role'],
            'permissions'   => $permissions,
            'status'        => 'active', // active immediately since we create the account
            'invited_by'    => auth()->id(),
            'invite_token'  => Str::random(32),
            'invited_at'    => now(),
            'joined_at'     => now(),
        ]);

        // Try to send invite email (silently fails if mail not configured)
        $this->trySendInviteEmail($user, $business, $tempPassword);

        ActivityLog::record($business->id, 'team', 'created',
            "Added team member: {$validated['name']} ({$validated['email']}) as {$validated['role']}",
            ['member_id' => $member->id, 'new_user' => $isNewUser]
        );

        $message = $isNewUser
            ? "Team member added! Account created. Temporary password: <strong>{$tempPassword}</strong> — share this securely."
            : "Team member added! They can now access this business with their existing account.";

        return back()->with('success_html', $message);
    }

    // ─── Update (role + permissions) ─────────────────────────────────────────

    public function update(Request $request, BusinessMember $member)
    {
        $business = auth()->user()->business;
        $this->authorizeOwner($business);
        $this->authorizeMemberBelongsToBusiness($member, $business);

        $validated = $request->validate([
            'role'          => 'required|in:admin,manager,cashier,staff,viewer',
            'password'      => 'nullable|string|min:6|max:72',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(config('rbac.modules', []))),
        ]);

        $oldRole = $member->role;

        $member->update([
            'role'        => $validated['role'],
            'permissions' => $validated['permissions'] ?? [],
        ]);

        // Optional: reset the member's login password
        if (!empty($validated['password']) && $member->user) {
            $member->user->update(['password' => Hash::make($validated['password'])]);
        }

        ActivityLog::record($business->id, 'team', 'updated',
            "Updated member {$member->name}: role changed from {$oldRole} to {$validated['role']}",
            ['member_id' => $member->id, 'permissions' => $member->permissions]
        );

        return back()->with('success', 'Team member updated successfully.');
    }

    // ─── Toggle Status (suspend / activate) ──────────────────────────────────

    public function toggleStatus(BusinessMember $member)
    {
        $business = auth()->user()->business;
        $this->authorizeOwner($business);
        $this->authorizeMemberBelongsToBusiness($member, $business);

        $newStatus  = $member->status === 'active' ? 'suspended' : 'active';
        $member->update(['status' => $newStatus]);

        $action = $newStatus === 'active' ? 'activated' : 'suspended';
        ActivityLog::record($business->id, 'team', 'updated',
            "Team member {$member->name} was {$action}",
            ['member_id' => $member->id]
        );

        return back()->with('success', "Team member has been {$action}.");
    }

    // ─── Destroy ─────────────────────────────────────────────────────────────

    public function destroy(BusinessMember $member)
    {
        $business = auth()->user()->business;
        $this->authorizeOwner($business);
        $this->authorizeMemberBelongsToBusiness($member, $business);

        $name = $member->name;
        $member->delete();

        ActivityLog::record($business->id, 'team', 'deleted',
            "Removed team member: {$name}",
            ['member_id' => $member->id]
        );

        return back()->with('success', "{$name} has been removed from the team.");
    }

    // ─── Role Defaults (API) ──────────────────────────────────────────────────

    public function roleDefaults(string $role)
    {
        $defaults = config("rbac.role_defaults.{$role}", []);
        return response()->json(['permissions' => $defaults]);
    }

    // ─── Activity Logs (AJAX) ─────────────────────────────────────────────────

    public function logs(Request $request)
    {
        $business = auth()->user()->business;
        $this->authorizeOwner($business);

        $query = ActivityLog::forBusiness($business->id)->with('user');

        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(30);

        return response()->json($logs);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function authorizeOwner($business): void
    {
        if ($business->user_id !== auth()->id()) {
            abort(403, 'Only the business owner can manage the team.');
        }
    }

    private function authorizeMemberBelongsToBusiness(BusinessMember $member, $business): void
    {
        if ($member->business_id !== $business->id) {
            abort(403, 'That team member does not belong to this business.');
        }
    }

    private function trySendInviteEmail(User $user, $business, ?string $tempPassword): void
    {
        try {
            if (!config('mail.mailers.smtp.host')) return;

            Mail::send('emails.team-invite', [
                'user'         => $user,
                'business'     => $business,
                'tempPassword' => $tempPassword,
                'loginUrl'     => route('login'),
            ], function ($m) use ($user, $business) {
                $m->to($user->email, $user->name)
                  ->subject("You've been invited to {$business->name} on Shopybook");
            });
        } catch (\Throwable) {
            // Silently swallow mail errors
        }
    }
}
