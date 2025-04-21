<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuizAnswerController;

Route::prefix('quiz-answers')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::get('/',             [QuizAnswerController::class, 'index']);
        Route::get('/{quizAnswer}', [QuizAnswerController::class, 'show']);
        Route::post('/',            [QuizAnswerController::class, 'store']);
        Route::patch('/{quizAnswer}', [QuizAnswerController::class, 'update']);
        Route::delete('/{quizAnswer}',[QuizAnswerController::class, 'destroy']);
    });
