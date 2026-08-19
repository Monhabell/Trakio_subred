<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductivityRequest extends FormRequest
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
            'file_id' => 'bail|required|integer|exists:receptions,id',
            'base_id' => 'bail|required|integer|exists:formats,id',
            'observations' => 'nullable|string|max:1000',
            'quantity_users' => 'bail|nullable|integer|min:0',
            'type' => 'bail|required|integer|in:1,0'
        ];
    }

    public function messages(): array 
    {
        return [
            'file_id.required' => 'El número de ficha es requerido',
            'file_id.integer' => 'El número de ficha debe ser un entero',
            'file_id.exists' => 'El número de ficha no existe',
            'base_id.required' => 'Por favor especifique el id de la base',
            'base_id.integer' => 'El id de la base debe ser un entero',
            'base_id.exists' => 'La base seleccionada no existe',
            'observations.string' => 'Por favor escriba una observación válida',
            'observations.max' => 'Las observaciones exceden el número de caracteres permitido',
            'quantity_users.integer' => 'El número de usuarios debe ser un número entero',
            'quantity_users.min' => 'El número de usuarios no puede ser negativo',
            'type.required' => 'El tipo es requerido',
            'type.in' => 'El tipo seleccionado no es válido',
        ];
    }
}
