<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        // Apply filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs = $query->latest('id')->paginate(20)->withQueryString();
        
        $users = User::orderBy('name')->get();
        
        $actions = AuditLog::select('action')
            ->distinct()
            ->pluck('action')
            ->toArray();

        return view('audit_logs.index', compact('logs', 'users', 'actions'));
    }
}
