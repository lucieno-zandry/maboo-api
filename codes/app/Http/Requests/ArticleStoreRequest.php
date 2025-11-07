<?php

namespace App\Http\Requests;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ArticleStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Article::class);
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
            'author' => ['required', 'min:2', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'user_id' => ['required'],
            'sections' => ['array'],
            'sections.*.title' => ['required', 'min:2', 'max:255'],
            'sections.*.order' => ['required', 'numeric'],
            'sections.*.subsections' => ['array'],
            'sections.*.subsections.*.title' => ['required', 'min:2', 'max:255'],
            'sections.*.subsections.*.order' => ['required', 'numeric'],
            'sections.*.subsections.*.paragraphs' => ['array'],
            'sections.*.subsections.*.paragraphs.*.content' => ['required', 'min:2', 'max:2000'],
            'images' => ['array'],
            'images.*.caption' => ['min:2'],
            'images.*.order' => ['numeric'],
            'images.*.url' => ['required', File::image()],
        ];
    }

    public function prepareForValidation()
    {
        return $this->merge(['user_id' => auth()->id()]);
    }
}
