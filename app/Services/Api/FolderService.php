<?php

namespace App\Services\Api;

use App\Exceptions\FolderNotFoundException;
use App\Models\File;
use App\Models\Folder;

class FolderService
{
    /**
     * @param array $data Массив данных для создания папки.
     *
     * @return Folder Возвращает созданный объект папки (Folder).
     */
    public function createFolder(array $data)
    {
        $currentUserID    = \Auth::user()->id;
        $folderNameExists = Folder::where('user_id', $currentUserID)->get();
        $strArr           = [];

        foreach ($folderNameExists as $folderNameExist) {
            $strArr[] = $folderNameExist->name;
        }

        if (in_array($data['name'], $strArr)) {
            return ['error' => 'У вас уже есть папка с таким названием'];
        }

        $folder = Folder::create([
            'user_id' => $currentUserID,
            'name'    => $data["name"]
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
            throw new FolderNotFoundException('Папка не найдена');
        }
    }

    public function destroy(array $data): bool
    {
        $currentUserID = \Auth::id();
//        $absentFolderId      = NULL;
        $foundAll = true;

        foreach ($data['ids'] as $id) {
            $folder = Folder::where('user_id', $currentUserID)
                ->where('id', $id)
                ->first();

            if (empty($folder)) {
//                $absentFolderId = $id;
                $foundAll = false;
                break;
            }
        }

        if (!empty($foundAll)) {
            foreach ($data['ids'] as $id) {
                $folder = Folder::where('user_id', $currentUserID)
                    ->where('id', $id)
                    ->first();

                File::where('folder_id', $folder->id)->update(['deleted_at' => now()]);

                $folder->delete();
            }

            return true;
        } else {
            throw new FolderNotFoundException("Папка не найдена");
        }
    }
}
