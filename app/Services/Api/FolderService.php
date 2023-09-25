<?php

namespace App\Services\Api;

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
}
