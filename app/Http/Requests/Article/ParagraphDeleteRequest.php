<?php

namespace App\Http\Requests\Article;

use App\Models\Paragraph;
use Illuminate\Foundation\Http\FormRequest;

class ParagraphDeleteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // FIX: Vérification basique, permissions individuelles dans le contrôleur
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'paragraphs_ids' => ['required', 'regex:/^[\d]+([,\d])*$/']
        ];
    }
}
