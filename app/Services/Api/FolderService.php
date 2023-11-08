<?php

namespace App\Services\Api;

use App\Exceptions\FolderNameExistsException;
use App\Exceptions\FolderNotFoundException;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Services\Api\Validators\FolderValidator;

class FolderService
{
    /**
     * @throws FolderNameExistsException
     */
    public function store(string $name, User $user, FolderValidator $validator): Folder
    {
        $validator->checkFolderNameExists($user, $name);

        $folder = Folder::create([
            'user_id' => $user->id,
            'name'    => $name
        ]);

        return $folder;
    }

    /**
     * @throws FolderNameExistsException
     * @throws FolderNotFoundException
     */
    public function rename(string $name, User $user, int $folderID, FolderValidator $validator): Folder
    {
        $folder = Folder::where('user_id', $user->id)
            ->where('id', $folderID)
            ->first();

        $validator->checkFolderNameExists($user, $name);

        if (empty($folder)) {
            throw new FolderNotFoundException;
        }

        $folder->name = $name;
        $folder->saveOrFail();

        return $folder;
    }

    /**
     * @throws FolderNotFoundException
     */
    public function delete(array $folderIds, User $user): void
    {
        $allFoldersFound = true;

        foreach ($folderIds as $folderID) {
            $folder = Folder::where('user_id', $user->id)
                ->where('id', $folderID)
                ->first();

            if (empty($folder)) {
                $allFoldersFound = false;
                break;
            }
        }

        if (!$allFoldersFound) {
            throw new FolderNotFoundException;
        }

        foreach ($folderIds as $folderID) {
            $folder = Folder::where('user_id', $user->id)
                ->where('id', $folderID)
                ->first();

            File::where('folder_id', $folder->id)->update(['deleted_at' => now()]);

            $folder->delete();
        }
    }
}
