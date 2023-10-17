<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendEmailUpdateRequest extends FormRequest
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
            'new_email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')]
        ];
    }

    public function messages()
    {
        return [
            'new_email.required' => 'Email обязателен для заполнения.',
            'new_email.email'    => 'Пожалуйста, укажите корректный адрес электронной почты.',
            'new_email.max'      => 'Длина не должна превышать :max символов.',
            'new_email.unique'   => 'Пользователь с указанным адресом электронной почты уже существует.'
        ];
    }
}
