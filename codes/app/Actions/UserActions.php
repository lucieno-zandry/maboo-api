<?php

namespace App\Actions;

use App\Models\User;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class UserActions extends Actions
{
    protected $user;

    public function __construct()
    {
        $user = Auth::user();
        $this->user = User::find($user->id);
    }

    public function setUserToVerified(): UserActions
    {
        $this->user->email_verified_at = Helpers::getIsoString(date_create());
        return $this;
    }

    public function user(): User|Model
    {
        return $this->user;
    }

    public function save(): bool
    {
        return $this->user->save();
    }

    public function storeImage(UploadedFile $image): string
    {
        $path = $image->store('public/images/users');
        $path = str_replace('public/', '', $path);

        return $path;
    }

    public function getUsersOfType(string $type): Collection
    {
        $users = User::where('type', $type)->get();
        return $users;
    }

    public function notifyAdmins($notification)
    {
        $admins = $this->getUsersOfType(User::TYPE_ADMIN)->where('validated_at', '!==', null);

        if (!$admins->isEmpty()) {
            Notification::send($admins, $notification);
        }
    }
}