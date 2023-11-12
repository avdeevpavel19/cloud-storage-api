<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BaseException;
use App\Exceptions\DiskSpaceExhaustedException;
use App\Exceptions\FileNameExistsException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FilesNotFoundException;
use App\Exceptions\FolderNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DeleteFileRequest;
use App\Http\Requests\Api\UpdateNameFileRequest;
use App\Http\Requests\Api\UploadFileRequest;
use App\Services\Api\FileService;
use App\Services\Api\Validators\FileValidator;
use App\Services\Api\Validators\FolderValidator;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    use HttpResponse;

    private FileService     $service;
    private FolderValidator $folderValidator;
    private FileValidator   $fileValidator;

    public function __construct(FileService $service)
    {
        $this->service         = $service;
        $this->folderValidator = new FolderValidator;
        $this->fileValidator   = new FileValidator;
    }

    public function store(UploadFileRequest $request): array
    {
        try {
            $validatedData = $request->validated();
            $currentUser   = \Auth::user();

            $result = $this->service->upload($validatedData['file'], $validatedData, $currentUser, $this->folderValidator, $this->fileValidator);

            $uploadedFile = [
                'id'         => $result->id,
                'folder_id'  => $result->folder_id,
                'file'       => $result->file,
                'name'       => $result->name,
                'sizeMB'     => $result->sizeMB,
                'format'     => $result->format,
                'path'       => $result->path,
                'hash'       => $result->hash,
                'expires_at' => $result->expires_at,
            ];

            return $uploadedFile;
        } catch (DiskSpaceExhaustedException) {
            throw new DiskSpaceExhaustedException('Превышено допустимое дисковое пространство');
        } catch (FolderNotFoundException) {
            throw new FolderNotFoundException('Указанная папка не найдена');
        } catch (FileNameExistsException) {
            throw new FileNameExistsException('У вас уже есть файл с таким названием');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function getFilesByUser(): JsonResponse
    {
        try {
            $currentUser = \Auth::user();
            $userFiles   = $currentUser->files()->paginate(100);

            $userFilesData = [];

            foreach ($userFiles as $userFile) {
                $userFilesData[] = [
                    'id'         => $userFile->id,
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

            return $this->displayList($userFilesData);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function getFileByUser(int $id): array
    {
        try {
            $currentUser = \Auth::user();

            $userFile = $currentUser->files()->where('user_id', $currentUser->id)->where('id', $id)->first();

            if (empty($userFile)) {
                throw new FileNotFoundException('Файл не найден');
            }

            $userFileData = [
                'id'         => $userFile->id,
                'folder_id'  => $userFile->folder_id,
                'file'       => $userFile->file,
                'name'       => $userFile->name,
                'sizeMB'     => $userFile->sizeMB,
                'format'     => $userFile->format,
                'path'       => $userFile->path,
                'hash'       => $userFile->hash,
                'expires_at' => $userFile->expires_at,
            ];

            return $userFileData;
        } catch (FileNotFoundException $fileNotFoundException) {
            throw new FileNotFoundException($fileNotFoundException->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException($e->getMessage());
        }
    }

    public function download(int $fileID)
    {
        try {
            $currentUser = \Auth::user();

            $file = $currentUser->files()->where('user_id', $currentUser->id)->where('id', $fileID)->first();

            if ($file === NULL) {
                throw new FileNotFoundException('Файл не найден');
            }

            $filePath = storage_path('app/public/' . $file->path);

            return response()->download($filePath);
        } catch (FileNotFoundException $fileNotFoundException) {
            throw new FileNotFoundException($fileNotFoundException->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function rename(UpdateNameFileRequest $request, int $fileID): array
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $file           = $this->service->rename($validationData['name'], $fileID, $currentUser, $this->fileValidator);

            $fileData = [
                'id'        => $file->id,
                'folder_id' => $file->folder_id,
                'file'      => $file->file,
                'name'      => $file->name,
                'sizeMB'    => $file->sizeMB,
                'format'    => $file->format,
                'path'      => $file->path,
                'hash'      => $file->hash,
            ];

            return $fileData;
        } catch (FileNameExistsException) {
            throw new FileNameExistsException('У вас уже есть файл с таким названием');
        } catch (FileNotFoundException) {
            throw new FileNotFoundException('Файл не найден');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function deleteFiles(DeleteFileRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $this->service->destroy($validationData['ids'], $currentUser);

            return $this->info('Файлы успешно удалены');
        } catch (FilesNotFoundException) {
            throw new FilesNotFoundException('Один или несколько файлов не найдены');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function getSizeFilesInFolder(int $folderID): float
    {
        try {
            $currentUser = \Auth::user();

            $userOwnedFolder = $currentUser->folders()->where('id', $folderID)->where('user_id', $currentUser->id)->first();

            if ($userOwnedFolder === NULL) {
                throw new FolderNotFoundException('У вас нет такой папки');
            }

            $filesInFolder = $currentUser->files()->where('folder_id', $userOwnedFolder->id)->whereNull('deleted_at')->get();

            $totalSizeFilesInFolder = 0;

            foreach ($filesInFolder as $fileInFolder) {
                $totalSizeFilesInFolder += $fileInFolder['sizeMB'];
            }

            return (float)$totalSizeFilesInFolder;
        } catch (FolderNotFoundException $folderNotFoundException) {
            throw new FolderNotFoundException($folderNotFoundException->getMessage());
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException($e->getMessage());
        }
    }

    public function getSizeFilesOnDisk(): float
    {
        try {
            $currentUser = \Auth::user();
            $filesUser   = $currentUser->files()->where('user_id', $currentUser->id)->whereNull('deleted_at')->get();

            $totalSizeFiles = 0;

            foreach ($filesUser as $fileUser) {
                $totalSizeFiles += $fileUser->sizeMB;
            }

            return (float)$totalSizeFiles;
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }
}
