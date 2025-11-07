<?php

namespace App\Http\Requests\Article;

use App\Models\Image;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ImageCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('create', Image::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'images' => ['required', 'array'],
            'images.*.url' => ['required', File::image()],
            'images.*.caption' => ['nullable'],
            'images.*.order' => ['nullable'],
            'images.*.article_id' => ['required', 'exists:articles,id'],
        ];
    }
}
