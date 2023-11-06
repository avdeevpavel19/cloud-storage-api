<?php

namespace App\Services\Api;

use App\Exceptions\FileNameExistsException;
use App\Exceptions\FolderNameExistsException;
use App\Exceptions\FolderNotFoundException;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;

class FileAndFolderValidatorService
{
    private function getFilesForUser(User $user)
    {
        return File::where('user_id', $user->id)->get();
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    private function getFoldersForUser(User $user)
    {
        return Folder::where('user_id', $user->id)->get();
    }

    /**
     * @throws FolderNotFoundException
     */
    public function checkFolderIdExists(User $user, int $folderID): void
    {
        $userFolders = $this->getFoldersForUser($user);

        $existingFolderID = [];

        foreach ($userFolders as $userFolder) {
            $existingFolderID[] = $userFolder->id;
        }
        if (!in_array($folderID, $existingFolderID)) {
            throw new FolderNotFoundException;
        }
    }

    /**
     * @throws FileNameExistsException
     */
    public function checkFileNameExists(User $user, string $fileName): void
    {
        $userFiles         = $this->getFilesForUser($user);
        $existingFileNames = [];

        foreach ($userFiles as $userFile) {
            $existingFileNames[] = $userFile->name;
        }
        if (in_array($fileName, $existingFileNames)) {
            throw new FileNameExistsException;
        }
    }

    /**
     * @throws FolderNameExistsException
     */
    public function checkFolderNameExists(User $user, string $folderName): void
    {
        $userFolders         = $this->getFoldersForUser($user);
        $existingFolderNames = [];

        foreach ($userFolders as $userFolder) {
            $existingFolderNames[] = $userFolder->name;
        }
        if (in_array($folderName, $existingFolderNames)) {
            throw new FolderNameExistsException;
        }
    }
}
