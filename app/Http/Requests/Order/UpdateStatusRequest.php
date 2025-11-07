<?php

namespace App\Http\Requests\Order;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->type === User::TYPE_ADMIN || $this->user()->type === User::TYPE_SELLER;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'bail|required|numeric',
            'ids' => 'bail|required|array',
        ];
    }
}
