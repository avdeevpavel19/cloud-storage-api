<?php

namespace App\Http\Controllers\Api;

use App\DTO\Api\FolderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateFolderRequest;
use App\Models\Folder;
use App\Services\Api\FolderService;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    use HttpResponse;

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
                $folder->parent_folder_id,
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
}
