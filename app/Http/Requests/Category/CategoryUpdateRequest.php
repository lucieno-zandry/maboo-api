<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use App\Helpers\Helpers;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;

class CategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->type === User::TYPE_ADMIN;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'bail|required|exists:categories',
            'name' => 'bail|nullable|min:2|max:32',
            'image' => ['nullable', File::image()],
            'parent_id' => 'bail|nullable|exists:categories,id',
        ];
    }

    public function prepareForValidation()
    {
        $merge = [];

        if ($this->name) {
            $merge['name'] = Helpers::capitalizeWords($this->name);
        }

        $this->merge($merge);
    }
}
