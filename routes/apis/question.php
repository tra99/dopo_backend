<?php

use App\Http\Controllers\Api\QuestionController;
use Illuminate\Support\Facades\Route;

Route::prefix('questions')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [QuestionController::class, 'index']);
  Route::get('/{id}', [QuestionController::class, 'show']);
  Route::post('/', [QuestionController::class, 'store']);
  Route::patch('/{id}', [QuestionController::class, 'update']);
  Route::delete('/{id}', [QuestionController::class, 'destroy']);
});
