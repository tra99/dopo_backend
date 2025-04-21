<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;


class CategoryGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // storage\app\public\category_list.xlsx
        $path = storage_path('app/public/school_list.xlsx');

        if (!file_exists($path)) {
            throw new \Exception("Excel file does not exist at path: {$path}");
        }

        try {
            $i = 0;
            if ($xlsx = SimpleXLSX::parse($path)) {
                foreach ($xlsx->rows() as $row) {
                    if ($i > 0) {
                        $exists = DB::table('category_groups')->where('school_type_en', $row[14])->first();
                        if (!$exists) {
                            $data = [
                                'title' => 'ស្តង់ដារ​ និងសូចនករសម្រាប់' . $row[13],
                                'school_type_en' => $row[14],
                                'school_type_kh' => $row[13],
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            DB::table('category_groups')->insert($data);

                            // Seed School type
                            DB::table('school_types')->insert([
                                'en_name' => $row[14],
                                'kh_name' => $row[13]
                            ]);
                        }
                    }
                    $i++;
                }
                // DB::statement("SELECT setval('categories_id_seq', (SELECT MAX(id) FROM categories));");
            } else {
                echo SimpleXLSX::parseError();
            }
        } catch (\Exception $e) {

            echo 'Error: ' . $e->getMessage();
        }
    }
}
