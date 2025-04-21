<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CourseStudentController;

Route::prefix('course-students')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/',           [CourseStudentController::class, 'index']);
        Route::get('/{courseStudent}', [CourseStudentController::class, 'show']);
        Route::post('/',          [CourseStudentController::class, 'store']);
        Route::patch('/{courseStudent}', [CourseStudentController::class, 'update']);
        Route::delete('/{courseStudent}',[CourseStudentController::class, 'destroy']);
    });
