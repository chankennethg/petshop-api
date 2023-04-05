<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\PromotionController;

/**
 * ----------------------------------------
 * Route Group of JWT Auth protected routes
 * ----------------------------------------
 */
Route::middleware('auth.jwt')->group(function (): void {
    /** Admin Routes */
    Route::prefix('admin')->group(function (): void {
        Route::post('create', [AdminController::class, 'createAdmin']);
        Route::get('logout', [AdminController::class, 'logout']);
        Route::get('user-listing', [AdminController::class, 'list']);
        Route::put('user-edit/{uuid}', [AdminController::class, 'editUser']);
        Route::delete('user-delete/{uuid}', [AdminController::class, 'deleteUser']);
    });

    /** File routes */
    Route::prefix('file')->group(function (): void {
        Route::post('upload', [FileController::class, 'upload']);
    });
});

/**
 * ----------------------------------------
 * Public available routes
 * ----------------------------------------
 */
/** Public Admin routes */
Route::prefix('admin')->group(function (): void {
    Route::post('login', [AdminController::class, 'login']);
});

/** Public File routes */
Route::prefix('file')->group(function (): void {
    Route::get('{uuid}', [FileController::class, 'download']);
});

/** Public Main page routes */
Route::prefix('main')->group(function (): void {
    /** Promotions route */
    Route::get('promotions', [PromotionController::class, 'list']);

    /** Blog/Post Routes */
    Route::get('blog', [PostController::class, 'list']);
    Route::get('blog/{uuid}', [PostController::class, 'get']);
});
