<?php

use App\Http\Controllers\Api\MissionController;
use Illuminate\Support\Facades\Route;

Route::prefix('/missions')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [MissionController::class, 'index']);
  Route::post('/', [MissionController::class, 'store']);
  Route::get('/{id}', [MissionController::class, 'show']);
  Route::patch('/{id}', [MissionController::class, 'update']);
  Route::delete('/{id}', [MissionController::class, 'destroy']);
  Route::post('/{id}/school', [MissionController::class, 'updateMissionSchool']);
  Route::post('/{id}/tricking-report-notification', [MissionController::class, 'sendNotification']);
});
