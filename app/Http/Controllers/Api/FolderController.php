<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\BaseException;
use App\Exceptions\FolderNameExistsException;
use App\Exceptions\FolderNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateFolderRequest;
use App\Http\Requests\Api\DeleteFolderRequest;
use App\Http\Requests\Api\UpdateNameFolderRequest;
use App\Services\Api\FolderService;
use App\Services\Api\Validators\FolderValidator;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FolderController extends Controller
{
    use HttpResponse;

    private FolderService   $service;
    private FolderValidator $folderValidator;

    public function __construct(FolderService $service, FolderValidator $folderValidator)
    {
        $this->service         = $service;
        $this->folderValidator = $folderValidator;
    }

    public function store(CreateFolderRequest $request, FolderService $service): array
    {
        try {
            $currentUser   = \Auth::user();
            $validatedData = $request->validated();
            $folder        = $service->store($validatedData['name'], $currentUser, $this->folderValidator);

            return [
                'id'   => $folder->id,
                'name' => $folder->name,
            ];
        } catch (FolderNameExistsException) {
            throw new FolderNameExistsException('У вас уже есть папка с таким названием');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function getFoldersByUser(): JsonResponse
    {
        try {
            $currentUser = \Auth::user();

            $userFolders = $currentUser->folders()->where('user_id', $currentUser->id)->paginate(100);

            foreach ($userFolders as $folder) {
                $folderList[] = [
                    'id'   => $folder->id,
                    'name' => $folder->name,
                ];
            }

            return $this->displayList($folderList);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function rename(UpdateNameFolderRequest $request, int $id): array
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $folder         = $this->service->rename($validationData['name'], $currentUser, $id, $this->folderValidator);

            return [
                'id'   => $folder->id,
                'name' => $folder->name,
            ];
        } catch (FolderNotFoundException) {
            throw new FolderNotFoundException('Указанная папка не найдена');
        } catch (FolderNameExistsException) {
            throw new FolderNameExistsException('У вас уже есть папка с таким названием');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }

    public function delete(DeleteFolderRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $this->service->delete($validationData['ids'], $currentUser);

            return $this->info('Папки успешно удалены');
        } catch (FolderNotFoundException) {
            throw new FolderNotFoundException('Указанная папка не найдена');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw new BaseException('Unknown error');
        }
    }
}
