<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Rules\UserTypeIs;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'email', 'exists:users', new UserTypeIs(User::TYPE_ADMIN)],
            'password' => 'bail|required|min:6'
        ];
    }
}
