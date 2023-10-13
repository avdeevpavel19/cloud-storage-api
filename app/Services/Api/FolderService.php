<?php

namespace App\Services\Api;

use App\Events\FolderDeletingEvent;
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
            throw new FolderNotFoundException('Файл не найден');
        }
    }

    public function destroy(array $data): bool
    {
        $currentUserID = \Auth::id();
        $foundAll      = true;

        foreach ($data['ids'] as $id) {
            $folder = Folder::where('user_id', $currentUserID)
                ->where('id', $id)
                ->first();

            if (empty($folder)) {
                $foundAll = false;
                break;
            }
        }

        if (!empty($foundAll)) {
            foreach ($data['ids'] as $id) {
                $folder = Folder::where('user_id', $currentUserID)
                    ->where('id', $id)
                    ->first();

                event(new FolderDeletingEvent($folder));

                $folder->delete();
            }

            return true;
        } else {
            throw new FolderNotFoundException('Папка не найдена');
        }
    }
}
