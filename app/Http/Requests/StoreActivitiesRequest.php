<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreActivitiesRequest extends FormRequest
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
            'role_id' => ['required', 'integer'],
            'hour_value' => ['required', 'numeric'],
            'total_salary' => ['required', 'numeric']
        ];
    }

    public function messages()
    {
        return [
            'role_id.required' => 'El campo "perfil" es obligatorio.',
            'role_id.integer' => 'El campo "perfil" debe ser un número entero.',
            'hour_value.required' => 'Proporcione un valor para valor-hora',
            'hour_value.numeric' => 'El campo valor-hora debe ser un número',
            'total_salary.required' => 'Proporcione un valor para el total de honorarios',
            'total_salary.numeric' => 'El campo total de honorarios debe ser un número'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}