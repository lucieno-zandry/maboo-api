<?php

namespace App\Http\Requests\Product;

use App\Helpers\Helpers;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'bail|required|min:4|max:50',
            'description' => 'nullable',
            'price' => 'bail|required|numeric|min:5000|max:99999999',
            'inStock' => 'bail|nullable|numeric|min:1|max:99999',
            'user_id' => 'bail|required|exists:users,id',
            'category_id' => 'bail|required|exists:categories,id',
            'slug' => 'required',
            'images' => 'nullable',
            'images.*' => ['bail', File::image()],
            'sale_price' => 'nullable|numeric|max:99999999',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => Str::slug($this->title) . fake()->slug(),
            'title' => $this->title ? Helpers::capitalizeWords($this->title) : '',
            'user_id' => Auth::id(),
        ]);
    }
}
