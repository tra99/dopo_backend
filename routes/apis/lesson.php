<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LessonController;

Route::prefix('lessons')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/',       [LessonController::class, 'index']);
        Route::get('/{lesson}',[LessonController::class, 'show']);
        Route::post('/',      [LessonController::class, 'store']);
        Route::patch('/{lesson}', [LessonController::class, 'update']);
        Route::delete('/{lesson}',[LessonController::class, 'destroy']);
    });
