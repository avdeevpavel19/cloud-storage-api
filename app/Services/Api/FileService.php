<?php

namespace App\Services\Api;

use App\Exceptions\diskSpaceExhaustedException;
use App\Exceptions\FileNameExistsException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FilesNotFoundException;
use App\Exceptions\FolderNotFoundException;
use App\Models\File;
use App\Models\User;
use App\Services\Api\Validators\FileValidator;
use App\Services\Api\Validators\FolderValidator;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class FileService
{
    /**
     * @param UploadedFile    $file
     * @param array           $data
     * @param User            $user
     * @param FolderValidator $folderValidator
     * @param FileValidator   $fileValidator
     *
     * @return File
     * @throws FileNameExistsException
     * @throws FolderNotFoundException
     * @throws diskSpaceExhaustedException
     */
    public function upload(UploadedFile $file, array $data, User $user, FolderValidator $folderValidator, FileValidator $fileValidator): File
    {
        $fileSize          = $file->getSize();
        $fileSizeInMB      = $fileSize / (1024 * 1024);
        $formattedFileSize = number_format($fileSizeInMB, 2);

        $format = pathinfo($file->getClientOriginalName())['extension'];

        $uid = \Str::uuid();

        $filePath = $file->storeAs("files/$uid", "{$data['name']}.$format", 'public');

        $updatedDiskSpace = (float)$user->occupied_disk_space + (float)$formattedFileSize;

        $folderValidator->checkFolderIdExists($user, $data['folder_id']);
        $fileValidator->checkFileNameExists($user, $data['name']);

        if ($updatedDiskSpace >= 100) {
            throw new diskSpaceExhaustedException;
        }

        $user->occupied_disk_space = $updatedDiskSpace;
        $user->saveOrFail();

        return File::create([
            'user_id'     => \Auth::id(),
            'folder_id'   => (int)$data['folder_id'],
            'file'        => $file->getClientOriginalName(),
            'name'        => $data['name'],
            'sizeMB'      => $formattedFileSize,
            'format'      => $format,
            'path'        => $filePath,
            'hash'        => $file->hashName(),
            'expires_at'  => $data['expires_at'] ?? NULL,
            'uploaded_at' => Carbon::now(),
        ]);
    }

    /**
     * @param string        $fileName
     * @param int           $fileID
     * @param User          $user
     * @param FileValidator $validator
     *
     * @return File
     * @throws FileNameExistsException
     * @throws FileNotFoundException
     */
    public function rename(string $fileName, int $fileID, User $user, FileValidator $validator): File
    {
        $file = File::where('user_id', $user->id)
            ->where('id', $fileID)
            ->first();

        $validator->checkFileNameExists($user, $fileName);

        if ($file === NULL) {
            throw new FileNotFoundException;
        }

        $file->name = $fileName;
        $file->save();

        return $file;
    }

    /**
     * @param array $fileIds
     * @param User  $user
     *
     * @return void
     * @throws FilesNotFoundException
     */
    public function destroy(array $fileIds, User $user): void
    {
        $filesToDelete = File::where('user_id', $user->id)
            ->whereIn('id', $fileIds)
            ->get();

        if ($filesToDelete->isEmpty() || $filesToDelete->count() !== count($fileIds)) {
            throw new FilesNotFoundException;
        }

        $totalSizeDeletedFiles = $filesToDelete->sum('sizeMB');

        $user->occupied_disk_space -= (float)$totalSizeDeletedFiles;
        $user->save();

        $filesToDelete->each(function ($file) {
            $file->delete();
        });
    }
}
