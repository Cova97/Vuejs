<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePartRequest extends FormRequest
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
            'sku'         => ['required', 'string', 'max:50', 'unique:parts,sku'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'quantity'    => ['required', 'integer', 'min:0'],
            'price'       => ['required', 'numeric', 'min:0'],
            'active'      => ['boolean'],
        ];
    }
}
