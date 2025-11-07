<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class AdminActions
{
    public function admins(): Collection
    {
        $admins = User::where('type', User::TYPE_ADMIN)
            ->where('validated_at', '!=', null)
            ->get();

        return $admins;
    }

    public function adminIds(): array
    {
        $admins = $this
            ->admins()
            ->toArray();

        return array_map(fn($user) => $user['id'], $admins);
    }

    public function notify($notification)
    {
        $admins = $this->admins();

        if (!$admins->isEmpty()) {
            Notification::send($admins, $notification);
        }
    }
}