<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;


class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // storage\app\public\participant_list.xlsx
        $path = storage_path('app/public/participant_list.xlsx');

        if (!file_exists($path)) {
            throw new \Exception("Excel file does not exist at path: {$path}");
        }

        try {
            $i = 0;
            if ($xlsx = SimpleXLSX::parse($path)) {
                foreach ($xlsx->rows() as $row) {
                    if ($i > 0) {
                        DB::table('participants')->insert([
                            'name' => $row[0],
                            'phone' => $row[1],
                            'email' => $row[2],
                            'organization' => $row[3],
                            'created_at' => now(),
                            'updated_at' => now(),
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
