<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // storage\app\public\category_list.xlsx
        $path = storage_path('app/public/category_list.xlsx');

        if (!file_exists($path)) {
            throw new \Exception("Excel file does not exist at path: {$path}");
        }

        try {
            $i = 0;
            if ($xlsx = SimpleXLSX::parse($path)) {
                foreach ($xlsx->rows() as $row) {
                    if ($i > 0) {
                        $schoolType = DB::table('category_groups')->where('school_type_en', $row[4])->first();

                        $data = [
                            'id' => $row[0],
                            'title' => $row[1],
                            'type' => $row[2],
                            'school_type_id' => $schoolType->id,
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        if (strlen($row[3]) != 0) {
                            $data['parent_id'] = $row[3];
                        }
                        DB::table('categories')->insert($data);
                    }
                    $i++;
                }
                DB::statement("SELECT setval('categories_id_seq', (SELECT MAX(id) FROM categories));");
            } else {
                echo SimpleXLSX::parseError();
            }
        } catch (\Exception $e) {

            echo 'Error: ' . $e->getMessage();
        }
    }
}
