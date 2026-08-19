<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductivityRequest extends FormRequest
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
            'observations' => 'nullable|string|max:255',
            'type' => 'nullable|integer|in:1,0',
            'quantity_users' => 'nullable|integer|min:0'
        ];
    }

    public function messages(): array 
    {
        return [
            'observations.string' => 'Por favor escriba una observación válida',
            'observations.max' => 'Las observaciones exceden el número de caracteres permitidos',
        ];
    }
}
