<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateActivitiesRequest extends FormRequest
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
            'person_name' => ['required', 'string'],
            'contract_number' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'document' => ['required','numeric', 'digits_between:5,10'],
            'role' => ['required', 'string'],
            'total_fee' => ['required', 'numeric'],
            'init_date' => ['required', 'date'],
            'end_date' => ['required', 'date'], //'after:init_date'
            'text_specific_activities' => ['required', 'array'],
            'text_specific_activities.*' => ['string', 'regex:/^\d+\.\s.+/'],
            'text_activities_done' => ['required', 'array'],
            'text_activities_done.*' => ['string', 'min:10'],
            'total_fee_string' => ['required', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'person_name.required' => 'Por favor introducuzca un nombre',
            'person_name.string' => 'El nombre debe ser una cadena.',
            'contract_number.required' => 'El número de contrato es obligatorio.',
            'contract_number.regex' => 'El número de contrato debe estar en el formato XXXX-XXXX.',
            'document.required' => 'El número de documento es obligatorio.',
            'document.number' => 'El número de documento debe ser un número.',
            'document.digits_between' => 'El número de documento debe tener entre 5 y 10 dígitos.',
            'role.required' => 'El rol es obligatorio.',
            'role.string' => 'El rol debe ser una cadena.',
            'total_fee.required' => 'El total de honorarios es obligatorio',
            'total_fee.number' => 'El total de honorarios debe ser un número.',
            'init_date.required' => 'La fecha de inicio es obligatoria.',
            'init_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'text_specific_activities.required' => 'Debe introducir al menos una actividad específica.',
            'text_specific_activities.*.string' => 'Las actividades específicas deben ser cadenas.',
            'text_specific_activities.*.regex' => 'Las actividades específicas deben tener el formato X. Actividad.',
            'total_fee_string.required' => 'El valor en letras es obligatorio.'
        ];
    }
}