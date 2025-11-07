<?php

namespace App\Http\Requests\User;

use App\Helpers\Helpers;
use App\Rules\ExistingEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateRequest extends FormRequest
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
            'email' => ['bail', 'nullable', 'email', 'unique:users', new ExistingEmail],
            'password' => 'bail|nullable||min:6',
            'name' => 'bail|nullable|min:2',
            'firstname' => 'bail|nullable|min:2',
            'adress' => 'bail|nullable',
            'phone_number' => 'bail|nullable|min:8|max:15',
            'image' => ['bail', 'nullable', File::image()],
        ];
    }

    public function prepareForValidation()
    {
        $entriesToCapitalize = ['name', 'firstname'];
        $datas = $this->only($entriesToCapitalize);

        foreach ($datas as $key => $value) {
            if ($value) {
                $datas[$key] = Helpers::capitalizeWords($value);
            }
        }

        $this->merge($datas);
    }
}