<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;


class EvaluationCriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // storage\app\public\question_list.xlsx
        $path = storage_path('app/public/evaluation_criterias.xlsx');

        if (!file_exists($path)) {
            throw new \Exception("Excel file does not exist at path: {$path}");
        }

        try {
            $i = 0;
            if ($xlsx = SimpleXLSX::parse($path)) {
                foreach ($xlsx->rows() as $row) {
                    if ($i > 0) {
                        DB::table('evaluation_criterias')->insert([
                            'id' => $row[1],
                            'question_id' => $row[2],
                            'title' => $row[3],
                            'options' => $row[4],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    $i++;
                }
                DB::statement("SELECT setval('evaluation_criterias_id_seq', (SELECT MAX(id) FROM evaluation_criterias));");
            } else {
                echo SimpleXLSX::parseError();
            }
        } catch (\Exception $e) {

            echo 'Error: ' . $e->getMessage();
        }
    }
}
