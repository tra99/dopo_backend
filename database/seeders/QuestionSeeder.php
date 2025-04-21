<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;


class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // storage\app\public\question_list.xlsx
        $path = storage_path('app/public/question_list.xlsx');

        if (!file_exists($path)) {
            throw new \Exception("Excel file does not exist at path: {$path}");
        }

        try {
            $i = 0;
            if ($xlsx = SimpleXLSX::parse($path)) {
                foreach ($xlsx->rows() as $row) {
                    if ($i > 0) {
                        DB::table('questions')->insert([
                            'id' => $row[1],
                            'question' => $row[2],
                            'description' => $row[3],
                            'question_type' => $row[4],
                            'category_id' => $row[5],
                            'school_type' => $row[6],
                            'answer_option' => $row[7],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    $i++;
                }
                // After seeding data
                DB::statement("SELECT setval('questions_id_seq', (SELECT MAX(id) FROM questions));");

            } else {
                echo SimpleXLSX::parseError();
            }
        } catch (\Exception $e) {

            echo 'Error: ' . $e->getMessage();
        }
    }
}
