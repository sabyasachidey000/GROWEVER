<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\ActivityLog::with('user')->latest();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $logs = $query->paginate(50);
        $users = User::active()->get();

        return view('activity-logs.index', compact('logs', 'users'));
    }

    public function show($id)
    {
        $log = \App\Models\ActivityLog::with(['user', 'subject'])->findOrFail($id);
        return view('activity-logs.show', compact('log'));
    }
}
