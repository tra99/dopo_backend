<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;


class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // storage\app\public\school_list.xlsx
        $path = storage_path('app/public/school_list.xlsx');

        if (!file_exists($path)) {
            throw new \Exception("Excel file does not exist at path: {$path}");
        }

        try {
            $i = 0;
            if ($xlsx = SimpleXLSX::parse($path)) {
                foreach ($xlsx->rows() as $row) {
                    if ($i > 0) {
                        DB::table('schools')->updateOrInsert([
                            'school_code' => $row[0],
                        ],[
                            'school_code' => $row[0],
                            'school_name_kh' => $row[1],
                            'school_name_en' => $row[2],
                            'school_type_kh' => $row[13],
                            'school_type_en' => $row[14],
                            'sis' => $row[4],
                            'village_kh' => $row[5],
                            'village_en' => $row[6],
                            'commune_kh' => $row[7],
                            'commune_en' => $row[8],
                            'district_kh' => $row[9],
                            'district_en' => $row[10],
                            'province_kh' => $row[11],
                            'province_en' => $row[12],
                            'created_at' => now(),
                            'updated_at' => now(),
                            'principal_name_kh' => $row[15],
                            'principal_name_en' => $row[16],
                            'principal_gender' => $row[17],
                            'principal_phone' => $row[18]
                        ]);
                    }
                    $i++;
                }
            } else {
                echo SimpleXLSX::parseError();
            }
        } catch (\Exception $e) {

            echo 'Error: ' . $e->getMessage();
        }
    }
}
