<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'token'    => 'required',
            'password' => ['required', 'confirmed', 'min:2', 'max:30']
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'     => 'Токен обязателен для заполнения.',
            'password.required'  => 'Пароль обязателен для заполнения.',
            'password.confirmed' => 'Повторный пароль не совпадает.',
            'password.min'       => 'Пароль должен содержать минимум 2 символа.',
            'password.max'       => 'Пароль не должен превышать 30 символов.',
        ];
    }
}
