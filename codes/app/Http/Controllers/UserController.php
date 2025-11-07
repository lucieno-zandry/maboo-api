<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateRequest;
use App\Actions\UserActions;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function update(UpdateRequest $request, UserActions $userActions)
    {
        $user = $userActions->user();
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($user->image) {
                Helpers::deleteImage($user->image);
            }

            $image = $userActions->storeImage($request->image);
            $data['image'] = $image;
        }

        if (isset($data['email']) && !empty($data['email'])) {
            $data['email_verified_at'] = null;
        }

        $updated = $user->update($data);
        return response()->json(['updated' => $updated]);
    }

    public function changePassword(Request $request, UserActions $userActions)
    {
        $request->validate([
            'current_password' => 'bail|required|current_password',
            'password' => 'bail|required|min:6|max:32|confirmed',
        ]);

        $data = $request->only('password');
        $data['password'] = Hash::make($data['password']);

        $changed = $userActions->user()->update($data);
        return response()->json(['changed' => $changed]);
    }
}