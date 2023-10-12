<?php

namespace App\Services\Api;

use App\Exceptions\FolderNotFoundException;
use App\Models\Folder;

class FolderService
{
    /**
     * @param array $data Массив данных для создания папки.
     *
     * @return Folder Возвращает созданный объект папки (Folder).
     */
    public function createFolder(array $data): Folder
    {
        $currentUserID = \Auth::user()->id;
        $folder        = Folder::create([
            'user_id'          => $currentUserID,
            'parent_folder_id' => $data["parent_folder_id"] ?? NULL,
            'name'             => $data["name"]
        ]);

        return $folder;
    }

    public function rename(array $data, int $id): Folder
    {
        $currentUserID = \Auth::id();
        $folder        = Folder::where('user_id', $currentUserID)
            ->where('id', $id)
            ->first();

        if (!empty($folder)) {
            $folder->name = $data['name'];
            $folder->save();

            return $folder;
        } else {
            throw new FolderNotFoundException('Файл не найден');
        }
    }
}
