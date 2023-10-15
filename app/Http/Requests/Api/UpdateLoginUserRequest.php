<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoginUserRequest extends FormRequest
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
            'login' => ['required', 'string', 'max:255', 'unique:users']
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Логин пользователя обязателен для заполнения.',
            'login.string'   => 'Логин пользователя должен быть строкой.',
            'login.max'      => 'Логин пользователя не может быть длиннее :max символов.',
            'login.unique'   => 'Такой логин пользователя уже существует.'
        ];
    }
}
