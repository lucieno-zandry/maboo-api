<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\Helpers;
use Illuminate\Http\Request;

class SellerController extends Controller
{
    public function requests()
    {
        $sellers = User::where('type', User::TYPE_SELLER)
            ->where('validated_at', '=', null)
            ->get();

        return response()->json(['sellers' => $sellers]);
    }
}