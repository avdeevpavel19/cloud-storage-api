<?php

namespace App\Services\Api;

use App\Exceptions\diskSpaceExhaustedException;
use App\Exceptions\FileNotFoundException;
use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function upload(UploadedFile $file, array $data)
    {
        $fileSize          = $file->getSize();
        $fileSizeInMB      = $fileSize / (1024 * 1024);
        $formattedFileSize = number_format($fileSizeInMB, 2);

        if (!empty($file)) {
            $filePath = $file->store('files', 'public');
            $format   = pathinfo($file->getClientOriginalName())['extension'];

            $user             = User::where('id', \Auth::id())->first();
            $updatedDiskSpace = (float)$user->disk_space + (float)$formattedFileSize;

            $fileUserExists   = File::where('user_id', $user->id)->get();
            $folderUserExists = Folder::where('user_id', $user->id)->get();
            $strArrFileName   = [];
            $strArrFolderID   = [];

            foreach ($fileUserExists as $fileUserExist) {
                $strArrFileName[] = $fileUserExist['name'];
            }

            foreach ($folderUserExists as $folderUserExist) {
                $strArrFolderID[] = $folderUserExist['id'];
            }

            if (in_array($data['name'], $strArrFileName)) {
                return ['error' => 'У вас уже есть файл с таким названием'];
            }

            if (!in_array($data['folder_id'], $strArrFolderID)) {
                return ['error' => 'У вас нет указанной папки'];
            }

            if ($updatedDiskSpace <= 100) {
                $user->disk_space = $updatedDiskSpace;
                $user->save();

                $downloadFile = File::create([
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

                return $downloadFile;
            } else {
                throw new diskSpaceExhaustedException('Превышено допустимое дисковое пространство');
            }
        }

        throw new FileNotFoundException('Файл не найден');
    }

    public function rename(array $data, int $id): File
    {
        $currentUserID = \Auth::id();
        $file          = File::where('user_id', $currentUserID)
            ->where('id', $id)
            ->first();

        if (!empty($file)) {
            $file->name = $data['name'];
            $file->save();

            return $file;
        }

        throw new FileNotFoundException('Файл не найден');
    }

    public function destroy(array $data): bool
    {
        $currentUserID  = \Auth::id();
        $foundAll       = true;
        $totalSizeFiles = 0;
        $user           = User::where('id', \Auth::id())->first();

        foreach ($data['ids'] as $id) {
            $file = File::where('user_id', $currentUserID)
                ->where('id', $id)
                ->first();

            if (empty($file)) {
                $foundAll = false;
                break;
            }
        }

        if ($foundAll) {
            foreach ($data['ids'] as $id) {
                $file = File::where('user_id', $currentUserID)
                    ->where('id', $id)
                    ->first();

                $totalSizeFiles += $file->sizeMB;

                $file->delete();
            }
            $updatedDiskSpace = (float)$user->disk_space - (float)$totalSizeFiles;
            $user->disk_space = $updatedDiskSpace;
            $user->save();

            return true;
        } else {
            throw new FileNotFoundException('Один или несколько файлов не найдены');
        }
    }
}
