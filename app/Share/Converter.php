<?php

namespace App\Share;

use Carbon\Carbon;

class Converter
{
    public function translateModelName($modelName) {
        switch ($modelName) {
            case 'Answer':
                return 'ចម្លើយ';
            case 'AuditLog':
                return 'កំណត់ហេតុ';
            case 'Category':
                return 'ស្ដង់ដារ និងសូចនករ';
            case 'CategoryGroups':
                return 'ស្ដង់ដារ និងសូចនករ';
            case 'Challenge':
                return 'បញ្ហាប្រឈម';
            case 'Config':
                return 'ការកំណត់';
            case 'Dashboard':
                return 'ផ្ទាំងគ្រប់គ្រង';
            case 'Evaluation':
                return 'បញ្ជីវាយតម្លៃ';
            case 'EvaluationCriteria':
                return 'វាយតម្លៃសំណួរ';
            case 'Mission':
                return 'បេសកកម្ម';
            case 'MissionSchool':
                return 'បេសកកម្មនៃសាលា';
            case 'Notification':
                return 'ការ​ជូនដំណឹង';
            case 'Participant':
                return 'អ្នកចូលរួមបេសកម្ម';
            case 'Question':
                return 'សំណួរ';
            case 'Role':
                return 'តួនាទី';
            case 'School':
                return 'សាលារៀន';
            case 'SchoolParticipants':
                return 'អ្នកចូលរួមតាមសាលា';
            case 'SchoolSurvey':
                return 'ការស្ទង់មតិនៃសាលា';
            case 'SchoolType':
                return 'ប្រភេទសាលា';
            case 'SupportPhase':
                return 'ដំណាក់កាលគាំទ្រ';
            case 'Survey':
                return 'ការស្ទង់មតិ';
            case 'User':
                return 'អ្នកប្រើប្រាស់';
            default:
                // Fallback if none match
                return $modelName;
        }
    }

    public function convertToKhmerTimezone($globalDate)
    {
        // Make sure the global date is a Carbon instance, or create one
        $date = Carbon::parse($globalDate);

        // Convert to Khmer timezone (Asia/Phnom_Penh)
        $khmerDate = $date->setTimezone('Asia/Phnom_Penh');

        return $khmerDate;
    }
}
