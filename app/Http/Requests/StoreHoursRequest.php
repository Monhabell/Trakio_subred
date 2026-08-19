<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHoursRequest extends FormRequest
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
            'user_id' => 'required|integer',
            'number_month' => 'required|integer|max:12',
            'overtime_hours' => 'max:24',
            'month_hours' => 'max:184',
            'year' => 'integer|min:2024',
        ];
    }

    public function messages(): array 
    {
        return [
            'user_id.required' => 'El campo Usuario es obligatorio.',
            'user_id.integer' => 'El campo usuario debe ser un número entero.',
            'number_month.required' => 'El campo mes es obligatorio.',
            'number_month.integer' => 'El campo mes debe ser un número entero.',
            'number_month.max' => 'El campo mes no puede ser mayor a 12.',
            'month_hours.max' => 'Las horas para el mes no puede ser mayor a 184',
            'overtime_hours.max' => 'No se pueden asignar más de 24 horas extra',
            'year.required' => 'El campo año es obligatorio.',
            'year.integer' => 'El campo año debe ser un número entero.',
            'year.min' => 'Ingrese un año válido.'
        ];
    }
}