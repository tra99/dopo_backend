<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('users')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [UserController::class, 'index']);
  Route::post('/', [UserController::class, 'store']);
  Route::post('/import', [UserController::class, 'storeWithImport']);
  Route::get('/{id}', [UserController::class, 'show']);
  Route::patch('/{id}', [UserController::class, 'update']);
  Route::delete('/{id}', [UserController::class, 'destroy']);
});
