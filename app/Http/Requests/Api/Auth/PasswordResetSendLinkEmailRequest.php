<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetSendLinkEmailRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:users,email']
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email обязателен для заполнения.',
            'email.email'    => 'Email должен быть в формате email.',
            'email.exists'   => 'Указанного email не существует.',
        ];
    }
}