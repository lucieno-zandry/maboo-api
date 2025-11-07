<?php

namespace App\Http\Requests\ProductVariant;

use App\Models\ProductVariant;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;

class CreateProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::user()->can('create', ProductVariant::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['bail', 'required', 'exists:products,id'],
            'name' => ['bail', 'required', 'min:2', 'max:20'],
            'image' => ['bail', 'required', File::image()],
            'price' => ['bail', 'nullable', 'numeric', 'max:999999999'],
            'inStock' => ['bail', 'required', 'numeric'],
        ];
    }
}
