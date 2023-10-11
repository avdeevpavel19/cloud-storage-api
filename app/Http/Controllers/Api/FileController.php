<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadFileRequest;
use App\Services\Api\FileService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    use HttpResponse;

    public function store(UploadFileRequest $request, FileService $service): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $folder        = $service->upload($validatedData['file'], $validatedData);

            $downloadedFile = [
                'id'        => $folder->id,
                'user_id'   => $folder->user_id,
                'folder_id' => $folder->folder_id,
                'file'      => $folder->file,
                'name'      => $folder->name,
                'sizeMB'    => $folder->sizeMB,
                'format'    => $folder->format,
                'path'      => $folder->path,
                'hash'      => $folder->hash,
            ];

            return $this->created($downloadedFile);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
