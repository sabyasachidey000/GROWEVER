<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::forUser(auth()->id())
            ->latest()
            ->paginate(50);

        return view('notifications.index', compact('notifications'));
    }

    public function unread()
    {
        $notifications = Notification::forUser(auth()->id())
            ->unread()
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    public function count()
    {
        $count = Notification::forUser(auth()->id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Return recent unread breakdown/critical alerts for polling.
     * Returns only notifications from the last 2 minutes so the
     * browser popup fires once per event instead of repeatedly.
     */
    public function recentAlerts()
    {
        $alerts = Notification::forUser(auth()->id())
            ->unread()
            ->whereIn('type', ['machine_breakdown', 'quality_alert', 'production_target'])
            ->where('created_at', '>=', now()->subMinutes(2))
            ->latest()
            ->get()
            ->map(function ($notification) {
                return [
                    'id'       => $notification->id,
                    'type'     => $notification->type,
                    'title'    => $notification->title,
                    'message'  => $notification->message,
                    'priority' => $notification->priority,
                    'data'     => $notification->data,
                    'time'     => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json($alerts);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read.']);
    }

    public function markAllAsRead()
    {
        $notification = new Notification();
        $notification->markAllAsRead(auth()->id());

        return response()->json(['message' => 'All notifications marked as read.']);
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return response()->json(['message' => 'Notification deleted.']);
    }
}
