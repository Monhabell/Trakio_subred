<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required', 'captcha'],
        ];
    }

    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'Debe completar el reCAPTCHA.',
            'g-recaptcha-response.captcha' => 'La verificación del reCAPTCHA falló, inténtelo de nuevo.',
            'email.required' => 'Ingrese un correo',
            'email.email' => 'Ingrese un correo válido',
            'password.required' => 'Por favor ingrese una contraseña válida' 
        ];
    }
}
