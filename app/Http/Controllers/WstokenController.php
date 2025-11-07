<?php

namespace App\Http\Controllers;

use App\Actions\WstokenActions;
use Illuminate\Http\JsonResponse;

class WstokenController extends Controller
{
    public function token(WstokenActions $actions): JsonResponse
    {
        $token = $actions->getToken();
        return response()->json(['token' => $token->uuid]);
    }
}
