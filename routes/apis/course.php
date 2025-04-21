<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseController;

Route::prefix('courses')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/',        [CourseController::class, 'index']);
        Route::get('/{course}',[CourseController::class, 'show']);
        Route::post('/',       [CourseController::class, 'store']);
        Route::patch('/{course}', [CourseController::class, 'update']);
        Route::delete('/{course}',[CourseController::class, 'destroy']);
    });
