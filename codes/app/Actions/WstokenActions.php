<?php

namespace App\Actions;

use App\Models\Wstoken;
use Str;

class WstokenActions
{
    protected $tokenable_id;

    public function __construct(?int $tokenable_id = null)
    {
        $this->tokenable_id = $tokenable_id ?? auth()->id();
    }

    private function createToken(): Wstoken
    {
        $token = Wstoken::create([
            'user_id' => $this->tokenable_id,
            'uuid' => Str::random(),
        ]);

        return $token;
    }

    public function getToken(): WsToken
    {
        $token = Wstoken::where('user_id', $this->tokenable_id)->latest()->first();

        if (!$token) {
            $token = $this->createToken();
        }

        return $token;
    }
}