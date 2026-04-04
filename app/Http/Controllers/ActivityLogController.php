<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ActivityLog::query()
            ->with('user:id,name,email')
            ->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->string('subject_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->string('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->string('date_to'));
        }

        $activityLogs = $query->paginate(25)->withQueryString();

        // Online users: active in the last 5 minutes
        $onlineUsers = User::query()
            ->whereNotNull('last_active_at')
            ->where('last_active_at', '>=', now()->subMinutes(5))
            ->select('id', 'name', 'email', 'last_active_at')
            ->orderByDesc('last_active_at')
            ->get();

        // Users for filter dropdown
        $users = User::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Available subject types for filter
        $subjectTypes = ActivityLog::query()
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->map(fn (string $type) => [
                'value' => $type,
                'label' => class_basename($type),
            ]);

        return Inertia::render('ActivityLog/Index', [
            'activityLogs' => $activityLogs,
            'onlineUsers' => $onlineUsers,
            'users' => $users,
            'subjectTypes' => $subjectTypes,
            'filters' => $request->only(['user_id', 'action', 'subject_type', 'date_from', 'date_to']),
        ]);
    }
}
