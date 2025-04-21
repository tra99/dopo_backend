<?php

use App\Exports\MissionExport;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirebaseController;
use App\Mail\MissionEmail;
use App\Mail\ReportTrackingEmail;
use App\Mail\SchoolEvaluationEmail;
use App\Mail\SurveyEmail;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

Route::get('send-notification', [FirebaseController::class, 'sendTestNotification']);
Route::get('test', function () {
    return view('fcm_token');
});

Route::get('/testmail', function () {
    $start_date  = '2024-01-01';
    $end_date    = '2024-12-31';
    $purpose     = 'ស្រង់សំណួរក្នុងស្តង់ដារទី៣';
    $description = 'អ្វីៗអាចកើតទៅបាន កំឡុងពេលបេសកកម្មនេះ';
    $schools     = ['សាលារៀនទី១', 'សាលារៀនទី២'];

    $missionData = [
        'start_date'  => $start_date,
        'end_date'    => $end_date,
        'purpose'     => $purpose,
        'description' => $description,
        'schools'     => $schools,
    ];

    Mail::to('chhit085@gmail.com')->send(new MissionEmail($missionData));
    return response()->json(['message' => 'Email sent successfully']);
});

Route::get('/testSurvey', function () {
    $survey = [
        'title' => 'ការបំពេញការស្តង់ដារទី៣',
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31',
        'school_type' => 'អនុវិទ្យាល័យ',
        'description' => 'អ្វីៗអាចកើតទៅបាន កំឡុងពេលបេសកកម្មនេះ'
    ];

    Mail::to('chhit085@gmail.com')->send(new SurveyEmail($survey));

    return response()->json(['message' => 'Email send successfully']);
});

Route::get('/testSchoolEvaluation', function () {
    $school_evaluation = [
        'survey_title' => 'Test notification school evaluation',
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31',
        'school_name_kh' => 'ក្របីរៀល',
        'school_type_kh' => 'អនុវិទ្យាល័យ',
        'province_kh' => 'កែប',
        'score' => 50,
    ];

    Mail::to('chhit085@gmail.com')->send(new SchoolEvaluationEmail($school_evaluation));
    return response()->json(['message' => 'Email sent successfully']);
});

Route::get('/testReportTracking', function () {
    // Sample data for testing
    $trackingReport = [
        'mission_purpose' => 'Test Mission Purpose',
        'start_date' => '2025-04-01',
        'end_date' => '2025-04-07',
        'schools' => ['School A', 'School B', 'School C'],
    ];
    Mail::to('chhit085@gmail.com')->send(new ReportTrackingEmail($trackingReport));

    return 'Test email sent!';
});

Route::get('/exportMission', function () {
    return Excel::download(new MissionExport, 'mission.xlsx');
});

Route::get('{any}', function () {
    return view('application');
})->where('any', '^(?!api).*');

// require __DIR__ . '/auth.php';
