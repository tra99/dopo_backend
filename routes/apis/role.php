<?php

use App\Http\Controllers\Api\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->middleware('auth:sanctum')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [RoleController::class, 'index']);
  Route::get('/{id}', [RoleController::class, 'show']);
  Route::post('/', [RoleController::class, 'store']);
  Route::patch('/{id}', [RoleController::class, 'update']);
  Route::delete('/{id}', [RoleController::class, 'destroy']);
  Route::put('/{id}/change-status', [RoleController::class, 'changeStatus']);
});
