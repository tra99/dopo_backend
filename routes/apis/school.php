<?php

use App\Http\Controllers\Api\SchoolController;
use Illuminate\Support\Facades\Route;

Route::prefix('schools')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [SchoolController::class, 'list_schools']);
  Route::get('/{id}', [SchoolController::class, 'show']);
});
