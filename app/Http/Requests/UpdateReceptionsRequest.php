<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReceptionsRequest extends FormRequest
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
            'num_package' => 'required|integer',
            'environment_id' => 'required|exists:environments,id|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'num_package.required' => 'El campo número de paquetes es obligatorio.',
            'num_package.integer' => 'El campo número de paquetes debe ser un número entero.',
            'environment_id.required' => 'Por favor seleccione un entorno.',
            'environment_id.exists' => 'El entorno seleccionado no es existe.',
            'environment_id.integer' => 'El campo entorno debe ser un número entero.',
        ];
    }
}
