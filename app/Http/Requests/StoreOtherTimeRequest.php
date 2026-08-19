<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOtherTimeRequest extends FormRequest
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
            'time_requested' => 'required|max:8',
            'date_activity' => 'required|date',
            'technician_id' => 'required|integer',
            'description' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'time_requested.required' => 'Debe ingresar la cantidad de tiempo solicitada.',
            'time_requested.max' => 'La cantidad de tiempo solicitada no puede superar los 8 horas.',
            'date_activity.required' => 'Debe ingresar la fecha de la solicitud.',
            'date_activity.date' => 'El campo debe contener una fecha válida.',
            'technician_id.required' => 'Debe seleccionar un técnico.',
            'technician_id.integer' => 'El campo debe contener un número entero.',
            'description.required' => 'Debe ingresar una descripción.',
            'description.string' => 'El campo debe contener una cadena de texto.',
        ];
    }
}