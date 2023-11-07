<?php

namespace App\Services\Api\Validators;

use App\Exceptions\FolderNameExistsException;
use App\Exceptions\FolderNotFoundException;
use App\Models\User;

class FolderValidator
{
    /**
     * @throws FolderNotFoundException
     */
    public function checkFolderIdExists(User $user, int $folderID): void
    {
        $existingFolderID = [];

        foreach ($user->folders as $userFolder) {
            $existingFolderID[] = $userFolder->id;
        }
        if (!in_array($folderID, $existingFolderID)) {
            throw new FolderNotFoundException;
        }
    }

    /**
     * @throws FolderNameExistsException
     */
    public function checkFolderNameExists(User $user, string $folderName): void
    {
        $existingFolderNames = [];

        foreach ($user->folders as $userFolder) {
            $existingFolderNames[] = $userFolder->name;
        }

        if (in_array($folderName, $existingFolderNames)) {
            throw new FolderNameExistsException;
        }
    }
}
