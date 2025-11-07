<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\LoginRequest;
use App\Http\Requests\Customer\SignupRequest;
use App\Actions\AuthActions;
use App\Models\User;
use Illuminate\Http\Request;


class AuthController extends Controller
{
    public function getAuth(AuthActions $authActions)
    {
        return $authActions->getAuth(User::TYPE_CUSTOMER);
    }

    public function login(LoginRequest $request, AuthActions $authActions)
    {
        return $authActions->login($request);
    }

    public function signup(SignupRequest $request, AuthActions $authActions)
    {
        return $authActions->signup($request, User::TYPE_CUSTOMER);
    }

    public function resetPassword(Request $request, AuthActions $authActions)
    {
        return $authActions->resetPassword($request, User::TYPE_CUSTOMER);
    }
}