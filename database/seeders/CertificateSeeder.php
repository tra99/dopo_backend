<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run()
    {
        Certificate::create([
            'user_id' => 1,  // Assuming user with id 1 exists
            'course_id' => 1, // Assuming course with id 1 exists
            'date_of_certificate' => now()->addMonths(3)
        ]);
        Certificate::create([
            'user_id' => 2,  // Assuming user with id 2 exists
            'course_id' => 2, // Assuming course with id 2 exists
            'date_of_certificate' => now()->addMonths(6)
        ]);
    }
}
