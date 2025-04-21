<?php

use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    require __DIR__ . '/apis/mission.php';
    require __DIR__ . '/apis/evaluation.php';
    require __DIR__ . '/apis/role.php';
    require __DIR__ . '/apis/user.php';
    require __DIR__ . '/apis/category.php';
    require __DIR__ . '/apis/supportPhase.php';
    require __DIR__ . '/apis/survey.php';
    require __DIR__ . '/apis/question.php';
    require __DIR__ . '/apis/answer.php';
    require __DIR__ . '/apis/schoolType.php';
    require __DIR__ . '/apis/school.php';
    require __DIR__ . '/apis/auth.php';
    require __DIR__ . '/apis/participant.php';
    require __DIR__ . '/apis/file.php';
    require __DIR__ . '/apis/evaluationCriteria.php';
    require __DIR__ . '/apis/schoolParticipants.php';
    require __DIR__ . '/apis/categoryGroups.php';
    require __DIR__ . '/apis/challenge.php';
    require __DIR__ . '/apis/notification.php';
    require __DIR__ . '/apis/achievement.php';

    // Graph and Layout
    require __DIR__ . '/apis/widget.php';
    require __DIR__ . '/apis/dashboard.php';

    // Audit Log
    require __DIR__ . '/apis/auditLog.php';
});
