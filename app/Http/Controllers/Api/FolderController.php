<?php

namespace App\Http\Controllers\Api;

use App\DTO\Api\FolderDTO;
use App\Exceptions\FolderNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateFolderRequest;
use App\Http\Requests\Api\DeleteFolderRequest;
use App\Http\Requests\Api\UpdateNameFolderRequest;
use App\Models\File;
use App\Models\Folder;
use App\Services\Api\FolderService;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    use HttpResponse;

    private FolderService $service;

    public function __construct()
    {
        $this->service = new FolderService;
    }

    /**
     * @param CreateFolderRequest $request содержащит валидированные данные для создания папки.
     * @param FolderService       $service Сервис для работы с папками.
     *
     * @return JsonResponse Возвращает JSON-ответ с информацией о созданной папке или ошибкой.
     */
    public function store(CreateFolderRequest $request, FolderService $service): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $folder        = $service->createFolder($validatedData);

            $folderDTO = new FolderDTO(
                $folder->id,
                $folder->user_id,
                $folder->name,
            );

            return $this->success($folderDTO);
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function getFoldersByUser(): JsonResponse
    {
        try {
            $currentUserID = \Auth::id();
            $userFolders   = Folder::where('user_id', $currentUserID)->get();

            $userFoldersData = [];

            foreach ($userFolders as $userFolder) {
                $userFoldersData[] = [
                    'id'   => $userFolder->id,
                    'name' => $userFolder->name,
                ];
            }

            return $this->success($userFoldersData);
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function rename(UpdateNameFolderRequest $request, int $id): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $folder         = $this->service->rename($validationData, $id);

            $userFolderData = [
                'id'   => $folder->id,
                'name' => $folder->name,
            ];

            return $this->success($userFolderData);
        } catch (FolderNotFoundException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function deleteFolders(DeleteFolderRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $deletedFile    = $this->service->destroy($validationData);

            if ($deletedFile == true) {
                return $this->delete('Папка успешно удалена');
            }
        } catch (FolderNotFoundException $exception) {
            return $this->error($exception->getMessage());
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
