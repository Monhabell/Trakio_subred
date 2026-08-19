<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCorrectionRequest extends FormRequest
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
            'idProduct' => 'required|integer',
            'acciones' => 'required|integer',
            'registro_id' => 'required|integer',
            'no_pagina' => 'nullable|integer',
            'No_num' => 'nullable|integer',
            'fecha' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'idProduct.required' => 'El campo idProduct es obligatorio.',
            'idProduct.integer' => 'El campo idProduct debe ser un número entero.',
            'acciones.required' => 'El campo acciones es obligatorio.',
            'acciones.integer' => 'El campo acciones debe ser un número entero.',
            'registro_id.required' => 'El campo registro_id es obligatorio.',
            'registro_id.integer' => 'El campo registro_id debe ser un número entero.',
            'no_pagina.integer' => 'El campo no_pagina debe ser un número entero.',
            'No_num.integer' => 'El campo No_num debe ser un número entero.',
            'fecha.date' => 'El campo fecha debe ser una fecha válida.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Por favor revise que los datos ingresados sean correctos',
            'errors' => $validator->errors()
        ], 422));
    }
}