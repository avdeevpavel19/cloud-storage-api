<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\FolderNameExistsException;
use App\Exceptions\FolderNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateFolderRequest;
use App\Http\Requests\Api\DeleteFolderRequest;
use App\Http\Requests\Api\UpdateNameFolderRequest;
use App\Services\Api\FileAndFolderValidatorService;
use App\Services\Api\FolderService;
use App\Traits\HttpResponse;
use Exception;

class FolderController extends Controller
{
    use HttpResponse;

    private FolderService                 $service;
    private FileAndFolderValidatorService $validatorService;

    public function __construct(FolderService $service, FileAndFolderValidatorService $validatorService)
    {
        $this->service          = $service;
        $this->validatorService = $validatorService;
    }

    public function store(CreateFolderRequest $request, FolderService $service)
    {
        try {
            $currentUser   = \Auth::user();
            $validatedData = $request->validated();
            $createdFolder = $service->store($validatedData['name'], $currentUser, $this->validatorService);

            if ($createdFolder) {
                return $createdFolder;
            }
        } catch (FolderNameExistsException) {
            throw new FolderNameExistsException('У вас уже есть папка с таким названием');
        } catch (\Exception) {
            return $this->error('Unknown error');
        }
    }

    public function getFoldersByUser()
    {
        try {
            $currentUser = \Auth::user();
            $result      = $this->service->getFoldersByUser($currentUser);

            return $this->displayList($result);
        } catch (Exception) {
            return $this->error('Unknown error');
        }
    }

    public function rename(UpdateNameFolderRequest $request, int $id)
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $folder         = $this->service->rename($validationData['name'], $currentUser, $id, $this->validatorService);

            if ($folder) {
                return $folder;
            }
        } catch (FolderNotFoundException) {
            throw new FolderNotFoundException('Указанная папка не найдена');
        } catch (FolderNameExistsException) {
            throw new FolderNameExistsException('У вас уже есть папка с таким названием');
        } catch (Exception) {
            return 'Unknown error';
        }
    }

    public function delete(DeleteFolderRequest $request)
    {
        try {
            $validationData = $request->validated();
            $currentUser    = \Auth::user();
            $this->service->delete($validationData['ids'], $currentUser);
        } catch (FolderNotFoundException) {
            throw new FolderNotFoundException('Указанная папка не найдена');
        } catch (Exception) {
            return 'Unknown error';
        }
    }
}
