<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
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
            'login'      => ['required', 'string', 'max:255', Rule::unique('users', 'login')],
            'email'      => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'disk_space' => ['integer', 'min:0', 'max:102400'],
            'password'   => ['required', 'string', 'min:4', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Логин является обязательным.',
            'login.string'   => 'Логин должен быть строкой.',
            'login.max'      => 'Логин не должен превышать :max символов.',
            'login.unique'   => 'Пользователь с таким логином уже существует.',

            'email.required' => 'Email является обязательным.',
            'email.string'   => 'Email должен быть строкой.',
            'email.email'    => 'Email должен быть действительным адресом электронной почты.',
            'email.max'      => 'Email не должен превышать :max символов.',
            'email.unique'   => 'Пользователь с таким email уже существует.',

            'disk_space.integer' => 'disk_space должен быть целым числом.',
            'disk_space.min'     => 'disk_space должен быть не менее :min.',
            'disk_space.max'     => 'disk_space не должен превышать :max.',

            'password.required'  => 'Пароль является обязательным.',
            'password.string'    => 'Пароль должен быть строкой.',
            'password.min'       => 'Пароль должен быть не менее :min символов.',
            'password.confirmed' => 'Подтверждение пароля не совпадает с полем пароль.',
        ];
    }
}
