<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePartRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('parts', 'sku')->ignore($this->route('part')),
            ],
            'name'        => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['sometimes', 'required', 'integer', 'min:0'],
            'price'       => ['sometimes', 'required', 'numeric', 'min:0'],
            'active'      => ['boolean'],
        ];
    }
}
