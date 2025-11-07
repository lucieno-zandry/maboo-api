<?php

namespace App\Actions\Mail;

use App\Mail\Auth\PasswordReset;
use App\Models\User;
use App\Actions\Actions;
use App\Helpers\Helpers;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Str;

class PasswordResetActions extends Actions
{
    protected $user;
    protected $token;
    protected $newPassword;
    protected $Model;

    public function getPasswordResetPath(): string
    {
        $prefix = $this->user->type === User::TYPE_CUSTOMER ? '' : '/' . strtolower($this->user->type);
        return  "$prefix/auth/reset-password/";
    }

    public function __construct()
    {
        $this->Model = DB::table('password_reset_tokens');
    }

    protected function sendEmail(): ?SentMessage
    {
        return Mail::to($this->user)->send(new PasswordReset($this->token, $this->getPasswordResetPath()));
    }

    protected function clearTokens(): int
    {
        $deleted = $this->Model->where('email', $this->user->email)->delete();
        return $deleted;
    }

    protected function createToken(): string
    {
        $token = Str::random(32);

        $this->clearTokens();

        $this->Model->insert([
            'email' => $this->user->email,
            'token' => $token,
            'created_at' => Helpers::getIsoString(date_create())
        ]);

        return $token;
    }

    protected function setTokenUser(): PasswordResetActions
    {
        $passwordResetToken = $this->Model
            ->select('email')
            ->where('token', $this->token)
            ->first();

        $user = User::where('email', $passwordResetToken->email)->first();
        $this->user = $user;

        return $this;
    }

    protected function changeToNewPassword(): bool
    {
        $changed = false;

        if ($this->newPassword) {
            if (!$this->user) {
                $this->setTokenUser();
            }

            $this->user->password = Hash::make($this->newPassword);
            $changed = $this->user->save();
        }

        return $changed;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function setToken(string $token): PasswordResetActions
    {
        $this->token = $token;
        return $this;
    }

    public function makePasswordReset(): bool
    {
        $this->token = $this->createToken();
        return boolval($this->sendEmail());
    }

    public function setNewPassword(string $newPassword): PasswordResetActions
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    public function reset(): PasswordResetActions
    {
        $this->changeToNewPassword();
        return $this;
    }

    public function getUser()
    {
        if (!$this->user) {
            $this->setTokenUser();
        }
        
        return $this->user;
    }
}