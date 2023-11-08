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

Route::group(['middleware' => ['auth:api', 'verified']], function () {
    Route::get('user', [UserController::class, 'getInfo']);
    Route::put('user/login', [UserController::class, 'updateLogin']);
    Route::put('user/email', [UserController::class, 'sendEmailUpdate']);
    Route::get('user/email/{hash}', [UserController::class, 'updateEmail']);
    Route::post('folder', [FolderController::class, 'store']);
    Route::post('upload-file', [FileController::class, 'store']);
    Route::get('files', [FileController::class, 'getFilesByUser']);
    Route::get('files/info/{id}', [FileController::class, 'getFileByUser']);
    Route::get('download-files/{id}', [FileController::class, 'download']);
    Route::put('files/{id}', [FileController::class, 'rename']);
    Route::get('folders', [FolderController::class, 'getFoldersByUser']);
    Route::put('folders/{id}', [FolderController::class, 'rename']);
    Route::delete('folders/', [FolderController::class, 'delete']);
    Route::delete('files/', [FileController::class, 'deleteFiles']);
    Route::get('folder/files-size', [FileController::class, 'getSizeFilesInFolder']);
    Route::get('files-size', [FileController::class, 'getSizeFilesOnDisk']);
});
