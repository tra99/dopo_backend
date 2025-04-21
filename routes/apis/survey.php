<?php

use App\Http\Controllers\Api\SurveyController;
use Illuminate\Support\Facades\Route;

Route::prefix('surveys')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [SurveyController::class, 'index']);
  Route::post('/', [SurveyController::class, 'store']);
  Route::get('/{id}', [SurveyController::class, 'show']);
  Route::patch('/{id}', [SurveyController::class, 'update']);
  Route::delete('/{id}', [SurveyController::class, 'destroy']);
});
