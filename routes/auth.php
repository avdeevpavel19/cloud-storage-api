<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FolderController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('create-user', [RegisterController::class, 'store']);
Route::post('login', [LoginController::class, 'store']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('user-info', [UserController::class, 'getInfo']);
    Route::post('create-folder', [FolderController::class, 'store']);
});
