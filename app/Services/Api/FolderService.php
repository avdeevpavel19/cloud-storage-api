<?php

namespace App\Services\Api;

use App\DTO\Api\FolderDTO;
use App\Exceptions\FolderNameExistsException;
use App\Exceptions\FolderNotFoundException;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;

class FolderService
{
    public function store(array $data, User $user): FolderDTO
    {
        $existingFolders     = Folder::where('user_id', $user->id)->get();
        $existingFolderNames = [];

        foreach ($existingFolders as $existingFolder) {
            $existingFolderNames[] = $existingFolder->name;
        }

        if (in_array($data['name'], $existingFolderNames)) {
            throw new FolderNameExistsException;
        }

        $folder = Folder::create([
            'user_id' => $user->id,
            'name'    => $data["name"]
        ]);

        $folderDTO = new FolderDTO(
            $folder->id,
            $folder->user_id,
            $folder->name,
        );

        return $folderDTO;
    }

    public function getFoldersByUser(User $user): array
    {
        $userFolders = Folder::where('user_id', $user->id)->get();

        foreach ($userFolders as $userFolder) {
            $folderDTO[] = new FolderDTO(
                $userFolder->id,
                $userFolder->user_id,
                $userFolder->name
            );
        }

        return $folderDTO;
    }

    public function rename(array $data, User $user, int $folderID): FolderDTO
    {
        $folder = Folder::where('user_id', $user->id)
            ->where('id', $folderID)
            ->first();

        if (!empty($folder)) {
            $folder->name = $data['name'];
            $folder->save();

            $folderDTO = new FolderDTO(
                $folder->id,
                $folder->user_id,
                $folder->name,
            );

            return $folderDTO;
        } else {
            throw new FolderNotFoundException;
        }
    }

    public function delete(array $data, User $user): string
    {
//        $absentFolderId      = NULL;
        $allFoldersFound = true;

        foreach ($data['ids'] as $id) {
            $folder = Folder::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (empty($folder)) {
//                $absentFolderId = $id;
                $allFoldersFound = false;
                break;
            }
        }

        if (!empty($allFoldersFound)) {
            foreach ($data['ids'] as $id) {
                $folder = Folder::where('user_id', $user->id)
                    ->where('id', $id)
                    ->first();

                File::where('folder_id', $folder->id)->update(['deleted_at' => now()]);

                $folder->delete();
            }

            return 'Папка успешно удалена';
        } else {
            throw new FolderNotFoundException;
        }
    }
}
