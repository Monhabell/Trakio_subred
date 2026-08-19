<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'header_time' => 'required|numeric|decimal:1,2',
            'body_time' => 'required|numeric|decimal:1,2',
            'environment_id' => 'integer|exists:environments,id',
            'sds_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Por favor ingrese un nombre para el formato',
            'name.string' => 'El nombre debe ser una cadena de texto',
            'header_time.required' => 'Por favor ingrese el tiempo de encabezado',
            'header_time.numeric' => 'El tiempo de encabezado debe ser un número',
            'header_time.double' => 'El tiempo de encabezado debe ser un número decimal',
            'body_time.required' => 'Por favor ingrese el tiempo por cada individuo/seguimiento',
            'body_time.numeric' => 'El tiempo del individuo/seguimiento debe ser un número',
            'body_time.double' => 'El tiempo del individuo/seguimiento debe ser un número decimal',
            'environment_id.integer' => 'El entorno debe ser un número entero',
            'environment_id.exists' => 'El entorno seleccionado no existe',
            'sds_id.required' => 'Por favor seleccione un SDS',
            'sds_id.integer' => 'El SDS debe ser un número entero',
        ];
    }
}
