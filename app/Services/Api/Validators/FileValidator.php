<?php

namespace App\Services\Api\Validators;

use App\Exceptions\FileNameExistsException;
use App\Models\User;

class FileValidator
{
    /**
     * @throws FileNameExistsException
     */
    public function checkFileNameExists(User $user, string $fileName): void
    {
        $existingFileNames = [];

        foreach ($user->files as $userFile) {
            $existingFileNames[] = $userFile->name;
        }

        if (in_array($fileName, $existingFileNames)) {
            throw new FileNameExistsException;
        }
    }
}
