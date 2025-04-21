<?php

use App\Http\Controllers\Api\AchievementController;
use Illuminate\Support\Facades\Route;

Route::prefix('achievements')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [AchievementController::class, 'index']);
  Route::get('/{id}', [AchievementController::class, 'show']);
  Route::post('/', [AchievementController::class, 'store']);
  Route::patch('/{id}', [AchievementController::class, 'update']);
  Route::delete('/{id}', [AchievementController::class, 'destroy']);
});
