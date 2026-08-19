<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDataUserRequest extends FormRequest
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
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'document' => 'required|numeric',
            'birthdate' => 'required|date',
            'phone' => 'required|numeric|digits:10',
            'address' => 'required',
            'sex' => 'required',
            'rh' => 'required',
            'contract_number' => 'required|numeric|digits:4',
            'contract_vig' => 'required|numeric|digits:4',
            'eps' => 'required',
            'afp' => 'required',
            'arl' => 'required',
            'ethnicity' => 'required',
            'caja' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'foto.required' => 'La imagen es obligatoria.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'El formato de la imagen debe ser JPEG, PNG o JPG.',
            'foto.max' => 'El tamaño máximo de la imagen es 2MB.',

            'document.required' => 'El número de documento es obligatorio.',
            'document.numeric' => 'El número de documento debe ser numérico.',

            'birthdate.required' => 'La fecha de nacimiento es obligatoria.',
            'birthdate.date' => 'La fecha de nacimiento debe ser una fecha válida.',

            'phone.required' => 'El número de teléfono es obligatorio.',
            'phone.numeric' => 'El número de teléfono debe ser numérico.',
            'phone.digits' => 'Proporcione un número de teléfon válido.',

            'address.required' => 'La dirección es obligatoria.',

            'sex.required' => 'El sexo es obligatorio.',

            'rh.required' => 'El grupo sanguineo es obligatorio.',

            'contract_number.required' => 'El número de contrato es obligatorio.',
            'contract_number.numeric' => 'El número de contrato debe ser numérico.',
            'contract_number.digits' => 'Proporcione un número de contrato válido.',

            'contract_vig.required' => 'La vigencia del contrato es obligatoria.',
            'contract_vig.numeric' => 'La vigencia del contrato debe ser numérica.',
            'contract_vig.digits' => 'Proporcione una vigencia de contrato válida.',

            'eps.required' => 'La EPS es obligatoria.',
            'afp.required' => 'La AFP es obligatoria.',
            'arl.required' => 'La ARL es obligatoria.',
            'ethnicity.required' => 'La étnica es obligatoria.',
            'caja.required' => 'El campo "caja de compensación" es obligatorio. Si no cuenta con una, seleccione "ninguna"'
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
