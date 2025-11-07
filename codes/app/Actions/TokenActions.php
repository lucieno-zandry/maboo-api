<?php

namespace App\Actions;

use App\Models\User;

class TokenActions extends Actions{
    protected $name;
    protected $user;

    public function __construct(string $name = 'app-token')
    {
        $this->name = $name;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function createToken(): string
    {
        $token = $this->user->createToken($this->name)->plainTextToken;
        return $token;
    }
}