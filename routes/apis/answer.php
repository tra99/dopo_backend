<?php

use App\Http\Controllers\Api\AnswerController;
use Illuminate\Support\Facades\Route;

Route::prefix('answers')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [AnswerController::class, 'index']);
  Route::get('/{id}', [AnswerController::class, 'show']);
  Route::post('/', [AnswerController::class, 'storeAndUpdate']);
  Route::patch('/{id}', [AnswerController::class, 'update']);
  Route::delete('/{id}', [AnswerController::class, 'destroy']);
});
