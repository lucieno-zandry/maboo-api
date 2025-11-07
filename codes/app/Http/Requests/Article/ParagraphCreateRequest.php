<?php

namespace App\Http\Requests\Article;

use App\Models\Paragraph;
use Illuminate\Foundation\Http\FormRequest;

class ParagraphCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Paragraph::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'min:2', 'max:2000'],
            'subsection_id' => ['required', 'exists:subsections,id'],
        ];
    }
}
