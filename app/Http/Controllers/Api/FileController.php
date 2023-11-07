<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\diskSpaceExhaustedException;
use App\Exceptions\FileNameExistsException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\FilesNotFoundException;
use App\Exceptions\FolderNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\DeleteFileRequest;
use App\Http\Requests\Api\UpdateNameFileRequest;
use App\Http\Requests\Api\UploadFileRequest;
use App\Models\File;
use App\Models\Folder;
use App\Services\Api\FileService;
use App\Services\Api\Validators\FileValidator;
use App\Services\Api\Validators\FolderValidator;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mockery\Exception;

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

            if ($result) {
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
            }

            return $uploadedFile;
        } catch (diskSpaceExhaustedException) {
            throw new diskSpaceExhaustedException('Превышено допустимое дисковое пространство');
        } catch (FolderNotFoundException) {
            throw new FolderNotFoundException('Указанная папка не найдена');
        } catch (FileNameExistsException) {
            throw new FileNameExistsException('У вас уже есть файл с таким названием');
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getFilesByUser(): JsonResponse
    {
        try {
            $currentUserID = \Auth::id();
            $userFiles     = File::where('user_id', $currentUserID)->paginate(8);

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
        } catch (Exception) {
            throw new \Exception('Unknown error');
        }
    }

    public function getFileByUser(int $id): array
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
//                'user_id'    => $userFile->user_id,
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
        } catch (Exception) {
            throw new \Exception('Unknown error');
        }
    }

    public function download(int $id)
    {
        try {
            $currentUserID = \Auth::id();
            $file          = File::where('user_id', 1)
                ->where('id', $id)
                ->first();

            if (empty($file)) {
                throw new FileNotFoundException('Файл не найден');
            }

            $filePath = storage_path('app/public/' . $file->path);

            return response()->download($filePath);
        } catch (Exception) {
            throw new \Exception('Unknown error');
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
//                'user_id'   => $file->user_id,
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
        } catch (Exception) {
            throw new \Exception('Unknown error');
        }
    }

    public function deleteFiles(DeleteFileRequest $request)
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $this->service->destroy($validationData['ids'], $currentUser);
        } catch (FilesNotFoundException) {
            throw new FilesNotFoundException('Один или несколько файлов не найдены');
        } catch (\Exception) {
            throw new \Exception('Unknown error');
        }
    }

    public function getSizeFilesInFolder(Request $request)
    {
        try {
            $currentUserID   = \Auth::id();
            $userOwnedFolder = Folder::where('id', $request->folder_id)->where('user_id', $currentUserID)->first();

            if (empty($userOwnedFolder)) {
                throw new FolderNotFoundException('В указанной папке нет файлов');
            }

            $filesInFolder = File::where('folder_id', $userOwnedFolder->id)->whereNull('deleted_at')->get();

            $totalSizeFilesInFolder = 0;

            foreach ($filesInFolder as $fileInFolder) {
                $totalSizeFilesInFolder += $fileInFolder['sizeMB'];
            }

            return (int)$totalSizeFilesInFolder;
        } catch (Exception) {
            throw new \Exception('Unknown error');
        }
    }

    public function getSizeFilesOnDisk()
    {
        try {
            $currentUserID = \Auth::id();
            $filesUser     = File::where('user_id', $currentUserID)->whereNull('deleted_at')->get();

            $totalSizeFiles = 0;

            foreach ($filesUser as $fileUser) {
                $totalSizeFiles += $fileUser->sizeMB;
            }

            return (int)$totalSizeFiles;
        } catch (Exception) {
            throw new \Exception('Unknown error');
        }
    }
}
