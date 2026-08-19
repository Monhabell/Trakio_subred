<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'environment' => ['required', 'integer', 'exists:environments,id'],
            'subnet' => ['required', 'integer', 'exists:subnets,id'],
            'g-recaptcha-response' => ['required', 'captcha'], // ✅ validación captcha
        ];
    }

    public function messages(): array{
        return [
            'name.required' => 'El campo nombre es obligatorio.',
            'last_name.required' => 'El campo apellido es obligatorio.',
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'Por favor, ingrese un correo electrónico válido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'role_id.required' => 'Debe seleccionar una posición válida.',
            'role_id.exists' => 'La posición seleccionada no existe.',
            'environment.required' => 'Debe seleccionar un entorno válido.',
            'environment.exists' => 'El entorno seleccionado no existe.',
            'subnet.required' => 'Debe seleccionar una subred válida.',
            'subnet.exists' => 'La subred seleccionada no existe.',
            'g-recaptcha-response.required' => 'Debe completar el reCAPTCHA.',
            'g-recaptcha-response.captcha' => 'La verificación del reCAPTCHA falló, inténtelo de nuevo.',
        ];
    }
}
