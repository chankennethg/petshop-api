<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\PromotionController;

Route::prefix('admin')->group(function (): void {
    Route::post('create', [AdminController::class, 'createAdmin']);
    Route::post('login', [AdminController::class, 'login'])->withoutMiddleware('auth.jwt');
    Route::get('logout', [AdminController::class, 'logout']);
    Route::get('user-listing', [AdminController::class, 'list']);
    Route::put('user-edit/{uuid}', [AdminController::class, 'editUser']);
    Route::delete('user-delete/{uuid}', [AdminController::class, 'deleteUser']);
});

Route::prefix('file')->group(function (): void {
    Route::post('upload', [FileController::class, 'upload']);
    Route::get('{uuid}', [FileController::class, 'download'])->withoutMiddleware('auth.jwt');
});

Route::prefix('main')->group(function (): void {
    Route::get('promotions', [PromotionController::class, 'list'])->withoutMiddleware('auth.jwt');
    Route::get('blog', [PostController::class, 'list'])->withoutMiddleware('auth.jwt');
    Route::get('blog/{uuid}', [PostController::class, 'get'])->withoutMiddleware('auth.jwt');
});
