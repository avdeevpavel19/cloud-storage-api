<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\FileNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateNameFileRequest;
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
            return $this->error('Unknown error');
        }
    }

    public function getFilesByUser(): JsonResponse
    {
        try {
            $currentUserID = \Auth::id();
            $userFiles     = File::where('user_id', $currentUserID)->get();

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
            return $this->error('Unknown error');
        }
    }

    public function getFileByUser(int $id): JsonResponse
    {
        try {
            $currentUserID = \Auth::id();

            $userFile = File::where('user_id', $currentUserID)
                ->where('id', $id)
                ->first();

            if (empty($userFile)) {
                throw new FileNotFoundException('Файл не найден');
            }

            $userFileData = [
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

            return $this->success($userFileData);
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function download(int $id)
    {
        try {
            $currentUserID = \Auth::id();
            $file          = File::where('user_id', $currentUserID)
                ->where('id', $id)
                ->first();

            if (empty($file)) {
                throw new FileNotFoundException('Файл не найден');
            }

            $filePath = storage_path('app/public/' . $file->path);

            return response()->download($filePath);
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function rename(UpdateNameFileRequest $request, FileService $service, int $id): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $file           = $service->rename($validationData, $id);

            $fileData = [
                'id'        => $file->id,
                'user_id'   => $file->user_id,
                'folder_id' => $file->folder_id,
                'file'      => $file->file,
                'name'      => $file->name,
                'sizeMB'    => $file->sizeMB,
                'format'    => $file->format,
                'path'      => $file->path,
                'hash'      => $file->hash,
            ];

            return $this->success($fileData);
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
