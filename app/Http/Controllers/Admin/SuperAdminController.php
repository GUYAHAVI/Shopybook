<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\PageVisit;
use App\Models\User;
use App\Models\Website;
use App\Services\ClaudeAPIService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalBusinesses = Business::count();
        $activeBusinesses = Business::where('active', true)->count();
        $totalWebsites = Website::count();

        $planDistribution = Business::selectRaw('plan, COUNT(*) as count')
            ->groupBy('plan')
            ->pluck('count', 'plan')
            ->toArray();

        $trialBusinesses = Business::where('on_trial', true)->count();
        $expiredTrials = Business::where('on_trial', true)
            ->where('trial_ends_at', '<', now())
            ->count();

        $recentUsers = User::latest()->limit(10)->get();
        $recentBusinesses = Business::with('user')->latest()->limit(10)->get();

        $activeToday = PageVisit::whereNotNull('user_id')
            ->where('created_at', '>=', Carbon::today())
            ->distinct('user_id')
            ->count('user_id');

        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $newBusinessesThisWeek = Business::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        $recentActivity = ActivityLog::with(['user', 'business'])
            ->latest()
            ->limit(20)
            ->get();

        // Online users (active sessions in last 30 minutes)
        $onlineUsers = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('sessions.last_activity', '>', now()->subMinutes(30)->timestamp)
            ->select('users.id', 'users.name', 'users.email', 'sessions.ip_address', 'sessions.last_activity')
            ->orderByDesc('sessions.last_activity')
            ->limit(15)
            ->get();

        $onlineCount = $onlineUsers->count();

        // Recent logins (first page visit per user in last 7 days = proxy for login)
        $recentLogins = PageVisit::whereNotNull('user_id')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->select('user_id', DB::raw('MIN(created_at) as first_visit'), DB::raw('MAX(created_at) as last_seen'), DB::raw('COUNT(*) as visit_count'))
            ->groupBy('user_id')
            ->orderByDesc('last_seen')
            ->limit(10)
            ->get()
            ->map(function ($row) {
                $user = User::find($row->user_id);
                $row->user_name = $user?->name ?? 'Unknown';
                $row->user_email = $user?->email ?? 'Unknown';
                return $row;
            });

        // Daily login counts for last 30 days (based on distinct users with page visits per day)
        $dailyLogins = PageVisit::whereNotNull('user_id')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, COUNT(DISTINCT user_id) as unique_users, COUNT(*) as total_visits')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalBusinesses', 'activeBusinesses', 'totalWebsites',
            'planDistribution', 'trialBusinesses', 'expiredTrials',
            'recentUsers', 'recentBusinesses', 'activeToday',
            'newUsersThisWeek', 'newBusinessesThisWeek', 'recentActivity',
            'onlineUsers', 'onlineCount', 'recentLogins', 'dailyLogins'
        ));
    }

    public function businesses(Request $request)
    {
        $query = Business::with(['user', 'website']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($plan = $request->input('plan')) {
            $query->where('plan', $plan);
        }

        if ($request->input('trial') === 'active') {
            $query->where('on_trial', true)->where('trial_ends_at', '>', now());
        }

        if ($request->input('trial') === 'expired') {
            $query->where('on_trial', true)->where('trial_ends_at', '<', now());
        }

        if ($request->input('website') === 'yes') {
            $query->whereHas('website');
        }

        if ($request->input('website') === 'no') {
            $query->whereDoesntHave('website');
        }

        $businesses = $query->latest()->paginate(20)->withQueryString();

        return view('admin.businesses.index', compact('businesses'));
    }

    public function showBusiness(Business $business)
    {
        $business->load(['user', 'website', 'products', 'orders', 'customers']);

        $pageVisits = PageVisit::where('business_id', $business->id)
            ->selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, COUNT(DISTINCT user_id) as unique_users, AVG(duration_ms) as avg_duration")
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();

        $activityLogs = ActivityLog::forBusiness($business->id)
            ->with('user')
            ->latest()
            ->limit(30)
            ->get();

        $subscriptionPayments = DB::table('subscription_payments')
            ->where('business_id', $business->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $lastActive = PageVisit::where('business_id', $business->id)
            ->latest()
            ->first();

        return view('admin.businesses.show', compact(
            'business', 'pageVisits', 'activityLogs', 'subscriptionPayments', 'lastActive'
        ));
    }

    public function users(Request $request)
    {
        $query = User::with('business');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->input('admin') === 'yes') {
            $query->where('is_admin', true);
        }

        if ($request->input('verified') === 'yes') {
            $query->whereNotNull('email_verified_at');
        }

        if ($request->input('verified') === 'no') {
            $query->whereNull('email_verified_at');
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $userStats = [];
        foreach ($users as $user) {
            $userStats[$user->id] = [
                'visits' => PageVisit::where('user_id', $user->id)->count(),
                'last_seen' => PageVisit::where('user_id', $user->id)->max('created_at'),
            ];
        }

        return view('admin.users.index', compact('users', 'userStats'));
    }

    public function showUser(User $user)
    {
        $user->load('business');

        $visitTrend = PageVisit::selectRaw('DATE(created_at) as day, COUNT(*) as visits')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, AVG(duration_ms) as avg_duration")
            ->where('user_id', $user->id)
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(15)
            ->get();

        $activityLogs = ActivityLog::forUser($user->id)
            ->latest()
            ->limit(30)
            ->get();

        $totalVisits = PageVisit::where('user_id', $user->id)->count();
        $lastSeen = PageVisit::where('user_id', $user->id)->max('created_at');

        return view('admin.users.show', compact(
            'user', 'visitTrend', 'topPages', 'activityLogs', 'totalVisits', 'lastSeen'
        ));
    }

    public function extendSubscription(Request $request, Business $business)
    {
        $validated = $request->validate([
            'plan' => 'required|in:free,premium,enterprise',
            'days' => 'required|integer|min:1|max:365',
            'reason' => 'nullable|string|max:500',
        ]);

        $plan = $validated['plan'];
        $days = $validated['days'];

        if ($plan === 'free') {
            $business->update([
                'plan' => 'free',
                'on_trial' => false,
                'trial_ends_at' => null,
                'upgraded_at' => null,
            ]);
        } else {
            $newTrialEnd = $business->trial_ends_at && $business->trial_ends_at->isFuture()
                ? $business->trial_ends_at->addDays($days)
                : now()->addDays($days);

            $business->update([
                'plan' => $plan,
                'on_trial' => true,
                'trial_ends_at' => $newTrialEnd,
                'upgraded_at' => now(),
            ]);
        }

        Log::info('Admin extended subscription', [
            'business_id' => $business->id,
            'business_name' => $business->name,
            'plan' => $plan,
            'days' => $days,
            'reason' => $validated['reason'] ?? null,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', "Subscription extended by {$days} days on {$plan} plan.");
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['is_admin' => !$user->is_admin]);

        Log::info('Admin toggled user admin status', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'is_admin' => $user->is_admin,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', "Admin status " . ($user->is_admin ? 'granted' : 'revoked') . " for {$user->email}.");
    }

    public function deleteUser(User $user)
    {
        if ($user->is_admin && User::where('is_admin', true)->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin user.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $email = $user->email;
        $user->delete();

        Log::info('Admin deleted user', [
            'user_email' => $email,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.users.index')->with('success', "User {$email} deleted.");
    }

    public function dormantUsers(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $days = in_array($days, [14, 30, 60, 90, 180]) ? $days : 30;
        $cutoff = Carbon::now()->subDays($days);

        $dormantUsers = User::whereDoesntHave('business', function ($q) use ($cutoff) {
            $q->where('updated_at', '>', $cutoff);
        })
        ->whereDoesntHave('pageVisits', function ($q) use ($cutoff) {
            $q->where('created_at', '>', $cutoff);
        })
        ->where('is_admin', false)
        ->with('business')
        ->latest()
        ->paginate(25)
        ->withQueryString();

        $dormantCount = User::whereDoesntHave('business', function ($q) use ($cutoff) {
            $q->where('updated_at', '>', $cutoff);
        })
        ->whereDoesntHave('pageVisits', function ($q) use ($cutoff) {
            $q->where('created_at', '>', $cutoff);
        })
        ->where('is_admin', false)
        ->count();

        return view('admin.users.dormant', compact('dormantUsers', 'dormantCount', 'days'));
    }

    public function deleteDormantUsers(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $count = 0;
        foreach ($validated['user_ids'] as $userId) {
            $user = User::find($userId);
            if (!$user || $user->is_admin || $user->id === auth()->id()) {
                continue;
            }
            $user->delete();
            $count++;
        }

        Log::info('Admin bulk deleted dormant users', [
            'count' => $count,
            'admin_id' => auth()->id(),
        ]);

        return back()->with('success', "{$count} dormant users deleted.");
    }

    public function usageAnalytics(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $days = in_array($days, [7, 14, 30, 90]) ? $days : 30;
        $since = Carbon::now()->subDays($days)->startOfDay();

        $topPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, COUNT(DISTINCT user_id) as unique_users, AVG(duration_ms) as avg_duration_ms")
            ->where('created_at', '>=', $since)
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(25)
            ->get();

        $leastVisitedPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, COUNT(DISTINCT user_id) as unique_users")
            ->where('created_at', '>=', $since)
            ->groupBy('page')
            ->orderBy('visits')
            ->limit(25)
            ->get();

        $errorPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as errors, MAX(status_code) as sample_status")
            ->where('created_at', '>=', $since)
            ->where('status_code', '>=', 400)
            ->groupBy('page')
            ->orderByDesc('errors')
            ->limit(15)
            ->get();

        $exitPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as exits")
            ->whereIn('id', function ($q) use ($since) {
                $q->selectRaw('MAX(id)')
                    ->from('page_visits')
                    ->where('created_at', '>=', $since)
                    ->whereNotNull('session_id')
                    ->groupBy('session_id');
            })
            ->groupBy('page')
            ->orderByDesc('exits')
            ->limit(15)
            ->get();

        $moduleActivity = ActivityLog::selectRaw('module, action, COUNT(*) as total')
            ->where('created_at', '>=', $since)
            ->groupBy('module', 'action')
            ->orderByDesc('total')
            ->get()
            ->groupBy('module');

        $perBusinessUsage = Business::leftJoin('page_visits', 'page_visits.business_id', '=', 'businesses.id')
            ->selectRaw('businesses.id, businesses.name, businesses.slug, COUNT(page_visits.id) as visits, MAX(page_visits.created_at) as last_active')
            ->where(function ($q) use ($since) {
                $q->whereNull('page_visits.created_at')
                  ->orWhere('page_visits.created_at', '>=', $since);
            })
            ->groupBy('businesses.id', 'businesses.name', 'businesses.slug')
            ->orderByDesc('visits')
            ->limit(20)
            ->get();

        return view('admin.analytics.usage', compact(
            'days', 'topPages', 'leastVisitedPages', 'errorPages', 'exitPages',
            'moduleActivity', 'perBusinessUsage'
        ));
    }

    public function aiAnalysis(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $days = in_array($days, [7, 14, 30, 90]) ? $days : 30;
        $since = Carbon::now()->subDays($days)->startOfDay();

        $topPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, COUNT(DISTINCT user_id) as unique_users, AVG(duration_ms) as avg_duration_ms")
            ->where('created_at', '>=', $since)
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(10)
            ->get();

        $leastVisited = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, COUNT(DISTINCT user_id) as unique_users")
            ->where('created_at', '>=', $since)
            ->groupBy('page')
            ->orderBy('visits')
            ->limit(10)
            ->get();

        $errorPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as errors, MAX(status_code) as sample_status")
            ->where('created_at', '>=', $since)
            ->where('status_code', '>=', 400)
            ->groupBy('page')
            ->orderByDesc('errors')
            ->limit(10)
            ->get();

        $exitPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as exits")
            ->whereIn('id', function ($q) use ($since) {
                $q->selectRaw('MAX(id)')
                    ->from('page_visits')
                    ->where('created_at', '>=', $since)
                    ->whereNotNull('session_id')
                    ->groupBy('session_id');
            })
            ->groupBy('page')
            ->orderByDesc('exits')
            ->limit(10)
            ->get();

        $moduleActivity = ActivityLog::selectRaw('module, action, COUNT(*) as total')
            ->where('created_at', '>=', $since)
            ->groupBy('module', 'action')
            ->orderByDesc('total')
            ->get();

        // Daily visits trend for line chart
        $dailyVisits = PageVisit::selectRaw('DATE(created_at) as date, COUNT(*) as visits, COUNT(DISTINCT user_id) as unique_users')
            ->where('created_at', '>=', $since)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Module totals for doughnut chart
        $moduleTotals = ActivityLog::selectRaw('module, COUNT(*) as total')
            ->where('created_at', '>=', $since)
            ->groupBy('module')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $totalUsers = User::count();
        $activeInPeriod = PageVisit::whereNotNull('user_id')
            ->where('created_at', '>=', $since)
            ->distinct('user_id')
            ->count('user_id');

        $analysis = null;
        $error = null;

        if ($request->has('analyze')) {
            try {
                $claude = new ClaudeAPIService();

                $engagementRate = $totalUsers > 0 ? round($activeInPeriod / $totalUsers * 100, 1) : 0;

                // Send only compact summary stats to Claude to minimize tokens
                $prompt = "You are a product analyst. Given these aggregated SaaS metrics (last {$days} days), provide a BRIEF summary.\n\n";
                $prompt .= "Users: {$totalUsers} total, {$activeInPeriod} active ({$engagementRate}% engagement).\n";
                $prompt .= "Top pages: " . $topPages->take(5)->map(fn($p) => "{$p->page}({$p->visits})")->implode(', ') . ".\n";
                $prompt .= "Least visited: " . $leastVisited->take(5)->map(fn($p) => "{$p->page}({$p->visits})")->implode(', ') . ".\n";
                $prompt .= "Error pages: " . $errorPages->take(5)->map(fn($p) => "{$p->page}({$p->errors})")->implode(', ') . ".\n";
                $prompt .= "Exit pages: " . $exitPages->take(5)->map(fn($p) => "{$p->page}({$p->exits})")->implode(', ') . ".\n";
                $prompt .= "Top modules: " . $moduleTotals->take(5)->map(fn($m) => "{$m->module}({$m->total})")->implode(', ') . ".\n";
                $prompt .= "\nProvide in plain text (no markdown):\n";
                $prompt .= "1. Key insight (1-2 sentences)\n";
                $prompt .= "2. Top 3 problem pages and why (1 line each)\n";
                $prompt .= "3. Top 3 recommendations (1 line each)\n";
                $prompt .= "Keep it under 200 words total.";

                $analysis = $claude->chatWithBusinessContext($prompt, [
                    'name' => 'SaaS Platform',
                    'type' => 'SaaS Platform',
                ]);
            } catch (\Exception $e) {
                $error = 'AI analysis failed: ' . $e->getMessage();
                Log::error('Admin AI analysis failed', ['error' => $e->getMessage()]);
            }
        }

        return view('admin.analytics.ai-analysis', compact(
            'days', 'topPages', 'leastVisited', 'errorPages', 'exitPages',
            'moduleActivity', 'moduleTotals', 'dailyVisits',
            'totalUsers', 'activeInPeriod', 'analysis', 'error'
        ));
    }

    public function subscriptions(Request $request)
    {
        $query = Business::with('user');

        if ($request->input('status') === 'trial') {
            $query->where('on_trial', true)->where('trial_ends_at', '>', now());
        }

        if ($request->input('status') === 'trial_expired') {
            $query->where('on_trial', true)->where('trial_ends_at', '<', now());
        }

        if ($request->input('status') === 'paid') {
            $query->whereIn('plan', ['premium', 'enterprise'])->where('on_trial', false);
        }

        if ($request->input('status') === 'free') {
            $query->where('plan', 'free')->where('on_trial', false);
        }

        if ($plan = $request->input('plan')) {
            $query->where('plan', $plan);
        }

        $businesses = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'free' => Business::where('plan', 'free')->where('on_trial', false)->count(),
            'premium' => Business::where('plan', 'premium')->where('on_trial', false)->count(),
            'enterprise' => Business::where('plan', 'enterprise')->where('on_trial', false)->count(),
            'trial_active' => Business::where('on_trial', true)->where('trial_ends_at', '>', now())->count(),
            'trial_expired' => Business::where('on_trial', true)->where('trial_ends_at', '<', now())->count(),
        ];

        $recentPayments = DB::table('subscription_payments')
            ->join('businesses', 'subscription_payments.business_id', '=', 'businesses.id')
            ->join('users', 'subscription_payments.user_id', '=', 'users.id')
            ->select(
                'subscription_payments.*',
                'businesses.name as business_name',
                'users.name as user_name',
                'users.email as user_email'
            )
            ->orderByDesc('subscription_payments.created_at')
            ->limit(15)
            ->get();

        return view('admin.subscriptions.index', compact('businesses', 'stats', 'recentPayments'));
    }

    public function websiteBuilder(Request $request)
    {
        $query = Business::with(['user', 'website']);

        if ($request->input('has_website') === 'yes') {
            $query->whereHas('website');
        }

        if ($request->input('has_website') === 'no') {
            $query->whereDoesntHave('website');
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('email', 'like', "%{$search}%");
                  });
            });
        }

        $businesses = $query->latest()->paginate(15)->withQueryString();

        $totalWithWebsite = Business::has('website')->count();
        $totalWithoutWebsite = Business::doesntHave('website')->count();

        return view('admin.website-builder.index', compact(
            'businesses', 'totalWithWebsite', 'totalWithoutWebsite'
        ));
    }
}
