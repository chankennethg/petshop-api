<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AdminController;

Route::prefix('admin')->group(function (): void {
    Route::post('create', [AdminController::class, 'store']);
    Route::post('login', [AdminController::class, 'login']);
    Route::get('logout', [AdminController::class, 'logout']);
    Route::get('user-listing', [AdminController::class, 'list']);
    Route::put('user-edit/{uuid}', [AdminController::class, 'editUser']);
    Route::delete('user-delete/{uuid}', [AdminController::class, 'deleteUser']);
});
