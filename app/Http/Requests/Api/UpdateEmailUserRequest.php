<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmailUserRequest extends FormRequest
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
            'current_email' => ['required', 'email', 'max:255', 'exists:users,email'],
            'new_email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
        ];
    }

    public function messages()
    {
        return [
            'current_email.required' => 'Текущий email обязателен для заполнения.',
            'current_email.email'    => 'Текущий email должен быть действительным адресом электронной почты.',
            'current_email.max'      => 'Текущий email не может быть длиннее :max символов.',
            'current_email.exists'   => 'Такой текущий email не найден в системе.',

            'new_email.required' => 'Новый email обязателен для заполнения.',
            'new_email.email'    => 'Новый email должен быть действительным адресом электронной почты.',
            'new_email.max'      => 'Новый email не может быть длиннее :max символов.',
            'new_email.unique'   => 'Такой новый email уже существует в системе.',
        ];
    }
}
