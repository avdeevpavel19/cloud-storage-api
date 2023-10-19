<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\FolderNameExistsException;
use App\Exceptions\FolderNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateFolderRequest;
use App\Http\Requests\Api\DeleteFolderRequest;
use App\Http\Requests\Api\UpdateNameFolderRequest;
use App\Services\Api\FolderService;
use App\Traits\HttpResponse;
use Exception;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    use HttpResponse;

    private FolderService $service;

    public function __construct(FolderService $service)
    {
        $this->service = $service;
    }

    public function store(CreateFolderRequest $request, FolderService $service): JsonResponse
    {
        try {
            $currentUser   = \Auth::user();
            $validatedData = $request->validated();
            $result        = $service->store($validatedData, $currentUser);

            if ($result) {
                return $this->success($result);
            }
        } catch (FolderNameExistsException $folderNameExistsException) {
            return $this->error($folderNameExistsException->getMessage());
        } catch (\Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function getFoldersByUser(): JsonResponse
    {
        try {
            $currentUser = \Auth::user();
            $result      = $this->service->getFoldersByUser($currentUser);

            if ($result) {
                return $this->success($result);
            }
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function rename(UpdateNameFolderRequest $request, int $id): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $folder         = $this->service->rename($validationData, $currentUser, $id);

            if ($folder) {
                return $this->success($folder);
            }
        } catch (FolderNotFoundException $e) {
            return $this->error($e->getMessage());
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }

    public function delete(DeleteFolderRequest $request): JsonResponse
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $result         = $this->service->delete($validationData, $currentUser);

            if ($result) {
                return $this->destroy($result);
            }
        } catch (FolderNotFoundException $exception) {
            return $this->error($exception->getMessage());
        } catch (Exception $e) {
            return $this->error('Unknown error');
        }
    }
}
