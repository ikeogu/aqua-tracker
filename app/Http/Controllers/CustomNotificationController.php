<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Http\Resources\CustomNotificationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomNotificationController extends Controller
{
    /**
     * Notifications
     *
     *
     */

    public function __invoke(Request $request) : JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:read,unread',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $notications = match ($request->type) {
            'read' => $user->readNotifications()->paginate($request->per_page ?? 10 ),
            'unread' => $user->unreadNotifications()->paginate($request->per_page ?? 10 ),
            default => $user->notifications()->paginate($request->per_page ?? 10 ),
        };

        return $this->success(
            message: "Notification sent successfully",
            data: [
                'unread' => $user->unreadNotifications()->count(),
                CustomNotificationResource::collection($notications)->response()->getData(true)
            ],
            code: HttpStatusCode::CREATED->value
        );
    }

    /**
     * Mark all Notification as read
     */
    public function markAllAsRead() : JsonResponse
    {

        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return $this->success(
            message: "Notification marked as read successfully",
            code: HttpStatusCode::CREATED->value
        );
    }
}