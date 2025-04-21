<?php

use App\Http\Controllers\Api\SchoolTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('school-types')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [SchoolTypeController::class, 'index']);
  Route::post('/', [SchoolTypeController::class, 'store']);
  Route::get('/{id}', [SchoolTypeController::class, 'show']);
  Route::patch('/{id}', [SchoolTypeController::class, 'update']);
  Route::delete('/{id}', [SchoolTypeController::class, 'destroy']);
  Route::get('parent/by-type', [SchoolTypeController::class, 'getParentByType']);
});
