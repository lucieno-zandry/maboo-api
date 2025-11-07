<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ProfessionalDataUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->type === User::TYPE_PROFESSIONAL;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'min:2'],
            'specialization' => ['nullable', 'min:2'],
            'experience' => ['nullable', 'numeric', 'max:80'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'description' => ['nullable'],
            'services' => ['nullable'],
            'user_id' => ['required']
        ];
    }

    public function prepareForValidation()
    {
        return $this->merge([
            'user_id' => auth()->id()
        ]);
    }
}
