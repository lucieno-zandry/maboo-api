<?php

namespace App\Actions\Mail;

use App\Mail\Auth\EmailConfirmation;
use App\Models\ConfirmationCodes;
use App\Actions\Actions;
use App\Helpers\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ConfirmationActions extends Actions
{
    protected $user;
    protected $code;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    protected function getValableCode(): ConfirmationCodes|Model|null
    {
        $now_date = date_create();
        $now_date_iso = Helpers::getIsoString($now_date);

        return ConfirmationCodes::where('user_id', $this->user->id)
            ->where('created_at', '<=', $now_date_iso)
            ->where('expires_at', '>', $now_date_iso)
            ->first();
    }

    protected function createCode(): ConfirmationCodes|Model
    {
        $code = random_int(100000, 999999);
        $expires_at = date_add(date_create(), date_interval_create_from_date_string('15 minutes'));

        return ConfirmationCodes::create([
            'content' => $code,
            'user_id' => $this->user->id,
            'expires_at' => Helpers::getIsoString($expires_at)
        ]);
    }

    protected function sendEmail()
    {
        return Mail::to($this->user)->send(new EmailConfirmation($this->code->content));
    }

    public function makeEmailConfirmation()
    {
        $sent = false;

        if (!$this->user->email_verified_at) {
            $this->code = $this->getValableCode() ?? $this->createCode();
            $sent = boolval($this->sendEmail());
        }

        return $sent;
    }

    public function hasMatched(string $requestCode)
    {
        $this->code = $this->getValableCode();

        if ($this->code && $requestCode !== $this->code->content) {
            throw ValidationException::withMessages([
                'code' => "Le code n'est pas valide",
            ]);
        }

        return $requestCode === $this->code->content;
    }
}