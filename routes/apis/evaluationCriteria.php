<?php

use App\Http\Controllers\Api\EvaluationCriteriaController;
use Illuminate\Support\Facades\Route;

Route::prefix('/evaluation-criterias')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [EvaluationCriteriaController::class, 'index']);
    Route::post('/', [EvaluationCriteriaController::class, 'store']);
    Route::get('/{id}', [EvaluationCriteriaController::class, 'show']);
    Route::patch('/{id}', [EvaluationCriteriaController::class, 'update']);
    Route::delete('/{id}', [EvaluationCriteriaController::class, 'destroy']);
});
