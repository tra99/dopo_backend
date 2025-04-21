<?php

use App\Http\Controllers\FileController;

// Define the routes

Route::prefix('files')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [FileController::class, 'getFile']);
    Route::post('/', [FileController::class, 'uploadFile']);
    Route::delete('/', [FileController::class, 'deleteFile']);
});
