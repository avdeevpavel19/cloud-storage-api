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
            'email'    => ['required', 'email', 'exists:users,email'],
            'token'    => 'required',
            'password' => ['required', 'confirmed', 'min:2', 'max:30']
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'     => 'Email обязателен для заполнения.',
            'email.email'        => 'Email должен быть действительным email-адресом.',
            'email.exists'       => 'Указанный адрес электронной почты не найден.',
            'token.required'     => 'Токен обязателен для заполнения.',
            'password.required'  => 'Пароль обязателен для заполнения.',
            'password.confirmed' => 'Повторный пароль не совпадает.',
            'password.min'       => 'Пароль должен содержать минимум 2 символа.',
            'password.max'       => 'Пароль не должен превышать 30 символов.',
        ];
    }
}
