<?php

use App\Http\Controllers\Api\SchoolController;
use Illuminate\Support\Facades\Route;

Route::prefix('mission-school')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [SchoolController::class, 'index']);
  Route::get('/{id}', [SchoolController::class, 'show']);
  Route::post('/', [SchoolController::class, 'store']);
  Route::patch('/{id}', [SchoolController::class, 'update']);
  Route::delete('/{id}', [SchoolController::class, 'destroy']);
});
