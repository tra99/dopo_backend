<?php

use App\Http\Controllers\Api\EvaluationController;
use Illuminate\Support\Facades\Route;

Route::prefix('/evaluations')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [EvaluationController::class, 'index']);
  Route::post('/', [EvaluationController::class, 'store']);
  Route::get('/{id}', [EvaluationController::class, 'show']);
  Route::patch('/{id}', [EvaluationController::class, 'update']);
  Route::delete('/{id}', [EvaluationController::class, 'destroy']);
});
