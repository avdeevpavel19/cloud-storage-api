<?php

namespace App\Services\Api;

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

        if ($file != NULL) {
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
        }

        return $downloadFile;
    }
}
