<?php

use App\Http\Controllers\Api\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::prefix('audit-logs')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [AuditLogController::class, 'index']);
  // Route::get('/{id}', [AuditLogController::class, 'show']);
  Route::post('/', [AuditLogController::class, 'logSchuduler']);
  Route::delete('/{id}', [AuditLogController::class, 'destroy']);
  Route::get('/export', [AuditLogController::class, 'exportCvs']);
});
