<?php

use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['auth:api', 'verified']], function () {
    Route::get('user-info', [UserController::class, 'getInfo']);
    Route::post('create-folder', [FolderController::class, 'store']);
    Route::post('upload-file', [FileController::class, 'store']);
    Route::get('my/files', [FileController::class, 'getFilesByUser']);
    Route::get('my/files/{id}', [FileController::class, 'getFileByUser']);
    Route::get('download-file/{id}', [FileController::class, 'download']);
    Route::put('my/files/rename/{id}', [FileController::class, 'rename']);
    Route::delete('my/files/', [FileController::class, 'deleteFiles']);
    Route::get('my/folders', [FolderController::class, 'getFoldersByUser']);
    Route::put('my/folders/rename/{id}', [FolderController::class, 'rename']);
    Route::delete('my/folders/', [FolderController::class, 'deleteFolders']);
    Route::get('my/folder/files-size', [FileController::class, 'getSizeFilesInFolder']);
    Route::get('my/disk/files-size', [FileController::class, 'getSizeFilesOnDisk']);
});
