<?php

namespace App\Http\Requests\Product;

use App\Helpers\Helpers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class UpdateProductRequest extends FormRequest
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
            'id' => 'required|exists:products',
            'title' => 'nullable|bail|min:4|max:50',
            'description' => 'nullable',
            'price' => 'nullable|bail|numeric|min:5000|max:99999999',
            'inStock' => 'nullable|bail|numeric|min:1|max:99999',
            'user_id' => 'nullable|bail|exists:users,id',
            'category_id' => 'nullable|bail|exists:categories,id',
            'slug' => 'nullable',
            'images' => 'nullable',
            'images.*' => ['bail', File::image()],
            'sale_price' => 'nullable|numeric|max:99999999',
        ];
    }

    public function prepareForValidation()
    {
        $merge = [];

        if ($this->title) {
            $merge['title'] = Helpers::capitalizeWords($this->title);
        }

        $this->merge($merge);
    }
}
