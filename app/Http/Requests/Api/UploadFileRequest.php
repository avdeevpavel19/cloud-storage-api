<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UploadFileRequest extends FormRequest
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
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
            'file'      => ['required', 'file', 'max:20971520'],
            'name'      => ['required', 'string', 'max:255', Rule::unique('files', 'name')],
            'expires_at' => ['nullable', 'date', 'after:' . now()]
        ];
    }

    public function messages()
    {
        return [
            'folder_id.integer' => 'Идентификатор папки должен быть целым числом.',
            'folder_id.exists'  => 'Указанной папки не существует.',
            'file.required'     => 'Файл обязателен для загрузки.',
            'file.file'         => 'Загруженный объект должен быть файлом.',
            'file.max'          => 'Размер файла не должен превышать 20 МБ.',
            'name.required'     => 'Имя файла обязательно для заполнения.',
            'name.string'       => 'Имя файла должно быть строкой.',
            'name.max'          => 'Имя файла не должно превышать 255 символов.',
            'name.unique'       => 'Файл с таким именем уже существует.',
            'expires_at.nullable' => 'Дата истечения срока хранения должна быть пустой или датой.',
            'expires_at.date' => 'Дата истечения срока хранения должна быть корректной датой.',
            'expires_at.after' => 'Дата истечения срока хранения должна быть позже текущей даты и времени.',
        ];
    }
}
