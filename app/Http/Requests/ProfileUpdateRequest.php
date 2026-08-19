<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\DataUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */

    public function rules(): array
    {
        return [
            'profile_img' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'document' => 'required|numeric',
            'birthdate' => 'required|date',
            'phone' => 'required|numeric|digits:10',
            'gender' => 'required',
            'contract_number' => [
                'required',
                'numeric',
                'digits:4',
                function ($attribute, $value, $fail) {
                    $contract = $value . '-' . $this->contract_vig;
                    $exists = DataUser::where('contract', $contract)
                        ->where('id', '<>', $this->user->id)
                        ->exists();

                    if ($exists) {
                        $fail('El contrato ya está registrado.');
                    }
                },
            ],
            'contract_vig' => 'required|numeric|digits:4',
            'eps' => 'required|string',
            'afp' => 'required|string',
            'arl' => 'required|string',
            'caja' => 'required|string',
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ];
    }

    public function messages(): array{
        return [
            'profile_img.mimes' => 'Sólo se permiten formatos jpg, jpeg o png',
            'profile_img.image' => 'El archivo debe ser una imagen',
            'profile_img.max' => 'El tamaño del archivo no debe superar los 2MB',
            'document.required' => 'El documento es requerido',
            'document.numeric' => 'El documento debe ser un número',
            'phone.digits' => 'El número de teléfono debe tener 10 dígitos',
            'phone.required' => 'El número de teléfono es requerido',
            'birthdate.required' => 'La fecha de nacimiento es requerida',
            'birthdate.date' => 'La fecha de nacimiento debe ser una fecha válida',
            'gender.required' => 'El género es requerido',
            'contract_number.required' => 'El número de contrato es requerido',
            'contract_number.numeric' => 'El número de contrato debe ser un número',
            'contract_number.digits' => 'El número de contrato debe tener 4 dígitos',
            'contract_vig.required' => 'La vigencia del contrato es requerida',
            'contract_vig.numeric' => 'La vigencia del contrato debe ser un número',
            'contract_vig.digits' => 'La vigencia del contrato debe tener 4 dígitos',
            'eps.required' => 'La EPS es requerida',
            'afp.required' => 'La AFP es requerida',
            'arl.required' => 'La ARL es requerida',
            'caja.required' => 'La caja de compensación es requerida',
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no puede tener más de 255 caracteres',
            'last_name.required' => 'El apellido es requerido',
            'last_name.max' => 'El apellido no puede tener más de 255 caracteres',
        ];
    }
}