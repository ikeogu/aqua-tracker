<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomNotificationController extends Controller
{
    //

    public function __invoke(Request $request) : JsonResponse
    {
        $request->validate([
            'type' => 'nullable|in:read,unread',
        ]);

        /** @var User $user */
        $user = auth()->user();

        $notications = match ($request->type) {
            'read' => $user->readNotifications()->paginate($request->per_page ?? 10 ),
            'unread' => $user->unreadNotifications()->paginate($request->per_page ?? 10 ),
            default => $user->notifications()->paginate($request->per_page ?? 10 ),
        };

        return $this->success(
            message: "Notification sent successfully",
            data: $notications,
            code: HttpStatusCode::CREATED->value
        );
    }

    public function markAllAsRead() : JsonResponse
    {

        /** @var User $user */
        $user = auth()->user();

        $user->unreadNotifications->markAsRead();

        return $this->success(
            message: "Notification marked as read successfully",
            code: HttpStatusCode::CREATED->value
        );
    }
}
