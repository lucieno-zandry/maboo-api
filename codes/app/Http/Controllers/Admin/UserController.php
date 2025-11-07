<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\User\AccountValidatedNotification;
use App\Helpers\Helpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class UserController extends Controller
{
    public function validate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        $ids = $validated['ids'];

        $updated = User::whereIn('id', $ids)->update([
            'validated_at' =>
                Helpers::getIsoString(date_create())
        ]);

        $users = User::whereIn('id', $ids)->get();

        if (!$users->isEmpty()) {
            Notification::send($users, new AccountValidatedNotification());
        }

        return response()->json(['updated' => $updated]);
    }

    public function users(): JsonResponse
    {
        $users = User::where('type', '!=', User::TYPE_ADMIN)->get();
        return response()->json(['users', $users]);
    }

    public function count(): JsonResponse
    {
        $count = User::where('type', '!=', User::TYPE_ADMIN)->count();
        return response()->json(['count' => $count]);
    }
}
