<?php

namespace App\Http\Requests\Article;

use App\Models\Subsection;
use Illuminate\Foundation\Http\FormRequest;

class SubsectionCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Subsection::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'min:2', 'max:255'],
            'order' => ['numeric'],
            'section_id' => ['required', 'exists:sections,id']
        ];
    }
}
