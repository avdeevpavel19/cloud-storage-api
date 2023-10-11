<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UploadFileRequest;
use App\Models\File;
use App\Services\Api\FileService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Mockery\Exception;

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

    public function getFilesByUser()
    {
        try {
            $currentUserID = \Auth::id();
            $userFiles     = File::where('user_id', '=', $currentUserID)->get();

            $userFilesData = [];

            foreach ($userFiles as $userFile) {
                $userFilesData[] = [
                    'id'        => $userFile->id,
                    'user_id'   => $userFile->user_id,
                    'folder_id' => $userFile->folder_id,
                    'file'      => $userFile->file,
                    'name'      => $userFile->name,
                    'sizeMB'    => $userFile->sizeMB,
                    'format'    => $userFile->format,
                    'path'      => $userFile->path,
                    'hash'      => $userFile->hash,
                ];
            }

            return $this->success($userFilesData);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
