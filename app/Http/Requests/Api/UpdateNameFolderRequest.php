<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNameFolderRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Имя папки обязательно для заполнения.',
            'name.string'   => 'Имя папки должно быть строкой.',
            'name.max'      => 'Имя папки не должно превышать 255 символов.',
        ];
    }
}
