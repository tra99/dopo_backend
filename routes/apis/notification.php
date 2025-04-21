<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('/notifications')->middleware('auth:sanctum')->group(function () {
  Route::get('/', [NotificationController::class, 'index']);
  Route::get('/{id}', [NotificationController::class, 'show']);
  Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
  Route::patch('/{id}/unread', [NotificationController::class, 'markAsUnread']);
  Route::patch('/all-read', [NotificationController::class, 'markAllAsRead']);
});
