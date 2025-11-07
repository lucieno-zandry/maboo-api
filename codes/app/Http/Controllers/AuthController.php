<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Actions\Mail\ConfirmationActions;
use App\Actions\Mail\PasswordResetActions;
use App\Actions\UserActions;
// use App\Rules\ExistingEmail;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function makeEmailConfirmation(ConfirmationActions $confirmationActions)
    {
        return response()->json(['sent' => $confirmationActions->makeEmailConfirmation()]);
    }

    public function matchConfirmationCode(ConfirmationActions $confirmationActions, Request $request, UserActions $userActions)
    {
        $request->validate(['code' => 'required|min:6']);

        $requestCode = $request->code;
        $matched = $confirmationActions->hasMatched($requestCode);

        if ($matched) {
            $userActions->setUserToVerified()->save();
        }

        return response()->json(['matched' => $matched]);
    }

    public function verifyEmailConformity(Request $request)
    {
        $request->validate(['email' => ['bail', 'required', 'email', 'unique:users']]);
        return response()->json(['valid' => true]);
    }

    public function forgottenPassword(Request $request, PasswordResetActions $passwordResetActions)
    {
        $request->validate(['email' => ['bail', 'required', 'email', 'exists:users']]);
        $email = $request->input('email');

        $sent = $passwordResetActions
            ->setUser(User::where('email', $email)->first())
            ->makePasswordReset();

        return response()->json(['sent' => $sent]);
    }
}