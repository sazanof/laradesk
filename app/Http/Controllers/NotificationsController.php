<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function getNotifications(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return $user->notifications;
    }

    public function getLastNotifications(Request $request)
    {
        $ar = [];
        /** @var User $user */
        $user = $request->user();
        $notifications = $user->notifications()->limit(50)->get();
        if (!empty($notifications)) {
            foreach ($notifications as $notification) {
                $ar[] = array_merge(
                    $notification->data,
                    [
                        'id' => $notification->id,
                        'read_at' => $notification->read_at
                    ]);
            }
        }
        return $ar;
    }

    public function markAsRead(string $id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $notification = $user->unreadNotifications()->where('id', $id)->first();
        $notification?->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAsUnread(string $id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $notification = $user->readNotifications()->where('id', $id)->first();
        $notification?->markAsUnread();
        return response()->json(['success' => true]);
    }

    public function deleteNotification(string $id, Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $s = false;
        if ($user->notifications()->where('id', $id)->delete()) {
            $s = true;
        }

        return response()->json(['success' => $s]);
    }

    public function deleteNotifications(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        $s = false;
        if ($user->notifications()->delete()) {
            $s = true;
        }

        return response()->json(['success' => $s]);
    }
}
