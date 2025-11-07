<?php

namespace App\Http\Requests\Admin;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;

class SignupRequest extends FormRequest
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
            'email' => 'bail|required|email|unique:users',
            'password' => 'bail|required|min:6',
            'name' => 'bail|required|min:2',
            'firstname' => 'bail|required|min:2',
        ];
    }

    public function prepareForValidation()
    {
        $name = $this->input('name');
        $firstname = $this->input('firstname');

        if ($name && $firstname)
            $this->merge([
                'name' => Helpers::capitalizeWords($name),
                'firstname' => Helpers::capitalizeWords($firstname),
            ]);
    }
}