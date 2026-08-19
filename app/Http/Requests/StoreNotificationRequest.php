<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
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
            'to_user_id' => 'required|integer|exists:users,id',           
            'message' => 'required|string|max:1000',
            'type' => 'required|string|max:255',            
            'other_time_id' => 'nullable|integer|exists:other_times,id'
        ];
    }

    public function messages(): array {
        return [
            'to_user_id.required' => 'El usuario que recibe la notificación es obligatorio',
            'to_user_id.integer' => 'El usuario que recibe la notificación debe ser un número entero',
            'to_user_id.exists' => 'El usuario que recibe la notificación no existe',
            'message.required' => 'El mensaje de la notificación es obligatorio',
            'message.string' => 'El mensaje de la notificación debe ser una cadena',
            'message.max' => 'El mensaje de la notificación no puede exceder los 1000 caracteres',
            'type.required' => 'El tipo de la notificación es obligatorio',
            'type.string' => 'El tipo de la notificación debe ser una cadena',
            'type.max' => 'El tipo de la notificación no puede exceder los 255 caracteres',
            'other_time_id.integer' => 'El id del tiempo debe ser un número entero',
            'other_time_id.exists' => 'El id del tiempo no existe'
        ];
    }
}
