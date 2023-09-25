<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'login'    => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:4'],
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Логин является обязательным.',
            'login.string'   => 'Логин должен быть строкой.',
            'login.max'      => 'Логин не должен превышать :max символов.',

            'password.required' => 'Пароль является обязательным.',
            'password.string'   => 'Пароль должен быть строкой.',
            'password.min'      => 'Пароль должен быть не менее :min символов.',
        ];
    }
}
