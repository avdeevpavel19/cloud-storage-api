<?php

namespace App\Services\Api;

use App\Exceptions\FileNotFoundException;
use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function upload(UploadedFile $file, array $data): File
    {
        $fileSize          = $file->getSize();
        $fileSizeInMB      = $fileSize / (1024 * 1024);
        $formattedFileSize = number_format($fileSizeInMB, 2);

        if (!empty($file)) {
            $filePath = $file->store('files', 'public');
            $format   = pathinfo($file->getClientOriginalName())['extension'];

            $downloadFile = File::create([
                'user_id'     => \Auth::id(),
                'folder_id'   => (int)$data['folder_id'],
                'file'        => $file->getClientOriginalName(),
                'name'        => $data['name'],
                'sizeMB'      => $formattedFileSize,
                'format'      => $format,
                'path'        => $filePath,
                'hash'        => $file->hashName(),
                'uploaded_at' => Carbon::now(),
            ]);

            return $downloadFile;
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
        $currentUserID = \Auth::id();
        $foundAll      = true;

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

                $file->delete();
            }

            return true;
        } else {
            throw new FileNotFoundException('Один или несколько файлов не найдены');
        }
    }
}
