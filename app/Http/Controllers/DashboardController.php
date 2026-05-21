<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin panel dashboard.
     */
    public function index()
    {
        // 1. Core counters
        $totalMemos = Memo::count();
        $totalUsers = User::count();
        $totalLogs = AuditLog::count();

        // 2. Recent uploads
        $recentMemos = Memo::with('uploadedBy')->latest()->take(5)->get();

        // 3. Memos by Department (from_department is standard)
        $departmentStats = Memo::select('from_department', DB::raw('count(*) as count'))
            ->groupBy('from_department')
            ->orderBy('count', 'desc')
            ->get();

        // 4. Memos by Category
        $categoryStats = Memo::select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // 5. Monthly Uploads Trend (last 6 months)
        // SQLite uses strftime for date formatting
        $monthlyStats = Memo::select(
            DB::raw("strftime('%Y-%m', memo_date) as month"),
            DB::raw('count(*) as count')
        )
            ->where('memo_date', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // Standardize monthly labels (e.g. "Jan", "Feb")
        $monthlyLabels = [];
        $monthlyCounts = [];
        foreach ($monthlyStats as $stat) {
            $date = \DateTime::createFromFormat('Y-m', $stat->month);
            $monthlyLabels[] = $date ? $date->format('M Y') : $stat->month;
            $monthlyCounts[] = (int) $stat->count;
        }

        // Return view with parameters
        return view('dashboard', compact(
            'totalMemos',
            'totalUsers',
            'totalLogs',
            'recentMemos',
            'departmentStats',
            'categoryStats',
            'monthlyLabels',
            'monthlyCounts'
        ));
    }
}
