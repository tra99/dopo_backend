<?php

use App\Http\Controllers\Api\SupportPhaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('support-phases')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [SupportPhaseController::class, 'index']);
  Route::post('/', [SupportPhaseController::class, 'store']);
  Route::get('/{id}', [SupportPhaseController::class, 'show']);
  Route::patch('/{id}', [SupportPhaseController::class, 'update']);
  Route::delete('/{id}', [SupportPhaseController::class, 'destroy']);
});
