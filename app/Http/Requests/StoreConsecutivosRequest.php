<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use PHPUnit\Framework\Constraint\IsTrue;

class StoreConsecutivosRequest extends FormRequest
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
            'ficha' => 'required',
            'localidad' => 'required|string|max:255',
            'upz' => 'required|string|max:255',
            'primer_nombre' => 'required|string|max:255',
            'segundo_nombre' => 'nullable|string|max:255',
            'primer_apellido' => 'required|string|max:255',
            'segundo_apellido' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'ficha.required' => 'El número de ficha es obligatorio',
            'localidad.required' => 'La localidad es obligatorio',
            'localidad.string' => 'La localidad debe ser una cadena de texto',
            'localidad.max' => 'El campo localidad no debe superar los 255 caracteres',
            'primer_nombre.required' => 'El primer nombre es obligatorio',
            'primer_nombre.string' => 'El primer nombre debe ser una cadena de texto',
            'primer_nombre.max' => 'El primer nombre no debe superar los 255 caracteres',
            'segundo_nombre.string' => 'El segundo nombre debe ser una cadena de texto',
            'segundo_nombre.max' => 'El segundo nombre no debe superar los 255 caracteres',
            'primer_apellido.required' => 'El primer apellido es obligatorio',
            'primer_apellido.string' => 'El primer apellido debe ser una cadena de texto',
            'primer_apellido.max' => 'El primer apellido no debe superar los 255 caracteres',
            'segundo_apellido.string' => 'El segundo apellido debe ser una cadena de texto',
            'segundo_apellido.max' => 'El segundo apellido no debe superar los 255 caracteres',
            'upz.required' => 'La UPZ es obligatoria',
            'upz.string' => 'La UPZ debe ser una cadena de texto',
            'upz.max' => 'La UPZ no debe superar los 255 caracteres',
        ];
    }
}
