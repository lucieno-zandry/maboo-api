<?php

namespace App\Http\Requests\Article;

use App\Models\Paragraph;
use Illuminate\Foundation\Http\FormRequest;

class ParagraphUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('update', $this->paragraph);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['min:2', 'max:2000'],
            'subsection_id' => ['exists:subsections,id'],
        ];
    }
}
