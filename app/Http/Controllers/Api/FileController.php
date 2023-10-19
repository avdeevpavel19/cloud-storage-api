<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\diskSpaceExhaustedException;
use App\Exceptions\FileNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DeleteFileRequest;
use App\Http\Requests\Api\UpdateNameFileRequest;
use App\Http\Requests\Api\UploadFileRequest;
use App\Models\File;
use App\Models\Folder;
use App\Services\Api\FileService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;

class FileController extends Controller
{
    use HttpResponse;

    private FileService $service;

    public function __construct()
    {
        $this->service = new FileService;
    }

    public function store(UploadFileRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $folder        = $this->service->upload($validatedData['file'], $validatedData);

            if (isset($folder['error'])) {
                return $this->message($folder['error']);
            }

            $downloadedFile = [
                'id'         => $folder->id,
                'user_id'    => $folder->user_id,
                'folder_id'  => $folder->folder_id,
                'file'       => $folder->file,
                'name'       => $folder->name,
                'sizeMB'     => $folder->sizeMB,
                'format'     => $folder->format,
                'path'       => $folder->path,
                'hash'       => $folder->hash,
                'expires_at' => $folder->expires_at,
            ];

            return $this->created($downloadedFile);
        } catch (diskSpaceExhaustedException $diskSpaceExhaustedException) {
            return $this->error($diskSpaceExhaustedException->getMessage());
        } catch (FileNotFoundException $foundException) {
            return $this->error($foundException->getMessage());
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
                    'id'         => $userFile->id,
                    'user_id'    => $userFile->user_id,
                    'folder_id'  => $userFile->folder_id,
                    'file'       => $userFile->file,
                    'name'       => $userFile->name,
                    'sizeMB'     => $userFile->sizeMB,
                    'format'     => $userFile->format,
                    'path'       => $userFile->path,
                    'hash'       => $userFile->hash,
                    'expires_at' => $userFile->expires_at,
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
                'id'         => $userFile->id,
                'user_id'    => $userFile->user_id,
                'folder_id'  => $userFile->folder_id,
                'file'       => $userFile->file,
                'name'       => $userFile->name,
                'sizeMB'     => $userFile->sizeMB,
                'format'     => $userFile->format,
                'path'       => $userFile->path,
                'hash'       => $userFile->hash,
                'expires_at' => $userFile->expires_at,
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

    public function rename(UpdateNameFileRequest $request, int $id): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $file           = $this->service->rename($validationData, $id);

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

    public function deleteFiles(DeleteFileRequest $request): JsonResponse
    {
        $validationData = $request->validated();
        $deletedFile    = $this->service->destroy($validationData);

        if ($deletedFile == true) {
            return $this->delete('Файлы успешно удалены');
        }
    }

    public function getSizeFilesInFolder(Request $request): JsonResponse
    {
        try {
            $userOwnedFolder = Folder::where('id', $request->folder_id)->where('user_id', \Auth::id())->first();

            if (empty($userOwnedFolder)) {
                return $this->notFound('Папка не найдена');
            }

            $filesInFolder = File::where('folder_id', $userOwnedFolder->id)->whereNull('deleted_at')->get();

            $totalSizeFilesInFolder = 0;

            foreach ($filesInFolder as $fileInFolder) {
                $totalSizeFilesInFolder += $fileInFolder['sizeMB'];
            }

            return $this->success("Размер всех файлов в папке ({$userOwnedFolder->id}) - $totalSizeFilesInFolder MB");
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function getSizeFilesOnDisk()
    {
        try {
            $filesUser = File::where('user_id', \Auth::id())->whereNull('deleted_at')->get();

            $totalSizeFiles = 0;

            foreach ($filesUser as $fileUser) {
                $totalSizeFiles += $fileUser->sizeMB;
            }

            return $this->success("Размер всех файлов на вашем диске - $totalSizeFiles MB");
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
