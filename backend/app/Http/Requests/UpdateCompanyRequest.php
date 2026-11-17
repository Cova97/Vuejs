<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
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
     * "sometimes" = revisa el campo solo si viene (al editar no hace falta mandar todo).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'    => ['sometimes', 'required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:30'],
            'rfc'     => [
                'sometimes',
                'required',
                'string',
                'between:12,13',
                // Único, pero sin contar a la propia empresa que se está editando.
                Rule::unique('companies', 'rfc')->ignore($this->route('company')),
            ],
        ];
    }
}
