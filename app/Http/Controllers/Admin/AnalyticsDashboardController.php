<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PageVisit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboardController extends Controller
{
    public function index(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $days = in_array($days, [7, 14, 30, 90]) ? $days : 30;
        $since = Carbon::now()->subDays($days)->startOfDay();

        $totalUsers = User::count();
        $newUsers = User::where('created_at', '>=', $since)->count();

        $activeToday = PageVisit::whereNotNull('user_id')
            ->where('created_at', '>=', Carbon::today())
            ->distinct('user_id')->count('user_id');

        $activeInPeriod = PageVisit::whereNotNull('user_id')
            ->where('created_at', '>=', $since)
            ->distinct('user_id')->count('user_id');

        // Daily active users trend
        $dauTrend = PageVisit::selectRaw('DATE(created_at) as day, COUNT(DISTINCT user_id) as users, COUNT(*) as visits')
            ->whereNotNull('user_id')
            ->where('created_at', '>=', $since)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        // Most visited pages
        $topPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, COUNT(DISTINCT user_id) as unique_users, AVG(duration_ms) as avg_duration_ms")
            ->where('created_at', '>=', $since)
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(15)
            ->get();

        // Slowest pages (potential performance bottlenecks)
        $slowestPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, AVG(duration_ms) as avg_duration_ms, MAX(duration_ms) as max_duration_ms")
            ->where('created_at', '>=', $since)
            ->whereNotNull('duration_ms')
            ->groupBy('page')
            ->having('visits', '>=', 5)
            ->orderByDesc('avg_duration_ms')
            ->limit(10)
            ->get();

        // Error-prone pages (4xx/5xx responses)
        $errorPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as errors, MAX(status_code) as sample_status")
            ->where('created_at', '>=', $since)
            ->where('status_code', '>=', 400)
            ->groupBy('page')
            ->orderByDesc('errors')
            ->limit(10)
            ->get();

        // Exit pages: last page of each session (where users drop off)
        $lastVisitIds = PageVisit::selectRaw('MAX(id) as id')
            ->where('created_at', '>=', $since)
            ->whereNotNull('session_id')
            ->groupBy('session_id');

        $exitPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as exits")
            ->whereIn('id', $lastVisitIds)
            ->groupBy('page')
            ->orderByDesc('exits')
            ->limit(10)
            ->get();

        // Per-user engagement summary
        $userStats = User::query()
            ->leftJoin('page_visits', 'page_visits.user_id', '=', 'users.id')
            ->selectRaw('users.id, users.name, users.email, users.created_at as registered_at,
                COUNT(page_visits.id) as total_visits,
                MAX(page_visits.created_at) as last_seen,
                COUNT(DISTINCT DATE(page_visits.created_at)) as active_days')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->orderByDesc('total_visits')
            ->get();

        $mostActiveUsers = $userStats->take(10);

        // Churn risk: users who have visited before but not in the last 7 days
        $inactiveUsers = $userStats
            ->filter(fn ($u) => $u->last_seen === null || Carbon::parse($u->last_seen)->lt(Carbon::now()->subDays(7)))
            ->sortByDesc('total_visits')
            ->take(15)
            ->values();

        // Action breakdown by module (from activity logs)
        $moduleActivity = ActivityLog::selectRaw('module, action, COUNT(*) as total')
            ->where('created_at', '>=', $since)
            ->groupBy('module', 'action')
            ->orderByDesc('total')
            ->get()
            ->groupBy('module');

        // Recent user actions feed
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->limit(25)
            ->get();

        return view('admin.analytics.index', compact(
            'days', 'totalUsers', 'newUsers', 'activeToday', 'activeInPeriod',
            'dauTrend', 'topPages', 'slowestPages', 'errorPages', 'exitPages',
            'mostActiveUsers', 'inactiveUsers', 'moduleActivity', 'recentActivity'
        ));
    }

    public function user(Request $request, User $user)
    {
        $days = (int) $request->input('days', 30);
        $days = in_array($days, [7, 14, 30, 90]) ? $days : 30;
        $since = Carbon::now()->subDays($days)->startOfDay();

        $visitTrend = PageVisit::selectRaw('DATE(created_at) as day, COUNT(*) as visits')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topPages = PageVisit::selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits, AVG(duration_ms) as avg_duration_ms")
            ->where('user_id', $user->id)
            ->where('created_at', '>=', $since)
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(15)
            ->get();

        $lastSeen = PageVisit::where('user_id', $user->id)->max('created_at');
        $totalVisits = PageVisit::where('user_id', $user->id)->count();

        $recentVisits = PageVisit::where('user_id', $user->id)
            ->latest()
            ->limit(50)
            ->get();

        $activityLogs = ActivityLog::forUser($user->id)
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.analytics.user', compact(
            'user', 'days', 'visitTrend', 'topPages', 'lastSeen', 'totalVisits',
            'recentVisits', 'activityLogs'
        ));
    }
}
