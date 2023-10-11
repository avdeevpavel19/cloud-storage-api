<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DeleteFileRequest extends FormRequest
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
            'ids' => ['required', 'array', 'exists:files,id'],
        ];
    }

    public function messages()
    {
        return [
            'ids.required' => ':attribute обязателен для заполнения.',
            'ids.exists'   => 'Указанного файла не существует.',
        ];
    }
}
