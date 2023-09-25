<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFolderRequest extends FormRequest
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
            'parent_folder_id' => ['nullable', 'integer'],
            'name'             => ['required', 'string', 'max:255', Rule::unique('folders', 'name')],
        ];
    }

    public function messages()
    {
        return [
            'parent_folder_id.integer' => 'Родительская папка должно быть целым числом.',
            'name.required'            => 'Имя обязательно для заполнения.',
            'name.string'              => 'Имя должно быть строкой.',
            'name.max'                 => 'Имя не должно превышать 255 символов.',
            'name.unique'              => 'Папка с таким именем уже существует.',
        ];
    }
}
