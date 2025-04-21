<?php

use App\Http\Controllers\Api\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::prefix('challenges')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [ChallengeController::class, 'index']);
  Route::post('/', [ChallengeController::class, 'store']);
  Route::get('/{id}', [ChallengeController::class, 'show']);
  Route::patch('/{id}', [ChallengeController::class, 'update']);
  Route::delete('/{id}', [ChallengeController::class, 'destroy']);
});
