<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseLessonStdController;

Route::prefix('lesson-statuses')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/',             [CourseLessonStdController::class, 'index']);
        Route::get('/{status}',     [CourseLessonStdController::class, 'show']);
        Route::post('/',            [CourseLessonStdController::class, 'store']);
        Route::patch('/{status}',   [CourseLessonStdController::class, 'update']);
        Route::delete('/{status}',  [CourseLessonStdController::class, 'destroy']);
    });
