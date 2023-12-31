<?php

use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;

Route::post('user', [RegisterController::class, 'store']);
Route::post('login', [LoginController::class, 'store']);

Route::post('password/link', [PasswordResetController::class, 'sendLinkEmail']);
Route::put('password/{token}', [PasswordResetController::class, 'reset']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/email/verification-notification', [VerificationController::class, 'sendVerificationNotification']);
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
});

Route::group(['middleware' => ['auth:api', 'verified']], function () {
    Route::post('/logout', [LoginController::class, 'logout']);
});
