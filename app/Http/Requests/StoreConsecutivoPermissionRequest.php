<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsecutivoPermissionRequest extends FormRequest
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
            'requested_quantity' => 'required|integer|min:1|max:20',
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'requested_quantity.required' => 'Debe ingresar la cantidad de consecutivos que necesita.',
            'requested_quantity.integer' => 'La cantidad debe ser un número entero.',
            'requested_quantity.min' => 'La cantidad debe ser al menos 1.',
            'requested_quantity.max' => 'La cantidad no puede superar los 20 consecutivos.',
            'reason.string' => 'El motivo debe ser una cadena de texto.',
            'reason.max' => 'El motivo no puede exceder los 500 caracteres.',
        ];
    }
}
