<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizQuestionController;

Route::prefix('quiz-questions')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/',               [QuizQuestionController::class, 'index']);
        Route::get('/{quizQuestion}', [QuizQuestionController::class, 'show']);
        Route::post('/',              [QuizQuestionController::class, 'store']);
        Route::patch('/{quizQuestion}', [QuizQuestionController::class, 'update']);
        Route::delete('/{quizQuestion}',[QuizQuestionController::class, 'destroy']);
    });
