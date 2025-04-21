<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Shuchkin\SimpleXLSX;


class SchoolStatisticSeeder extends Seeder
{
    private function toInt($value)
    {
        return is_numeric($value) ? (int) $value : 0;
    }
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
                        $school = School::where('school_code', $row[0])->first();

                        if ($school) {
                            DB::table('school_statistics')->insert([
                                'school_id' => $school->id,
                                'academic_year' => $this->toInt($row[30] ?? null),
                                'count_enrollment' => $this->toInt($row[19] ?? null),
                                'count_female_enrollment' => $this->toInt($row[20] ?? null),
                                'count_teaching_staff' => $this->toInt($row[21] ?? null),
                                'count_female_teaching_staff' => $this->toInt($row[22] ?? null),
                                'count_not_teaching_staff' => $this->toInt($row[23] ?? null),
                                'count_female_not_teaching_staff' => $this->toInt($row[24] ?? null),
                                'count_staff' => $this->toInt($row[25] ?? null),
                                'count_female_staff' => $this->toInt($row[26] ?? null),
                                'electricity' => $row[27],
                                'count_computer' => $this->toInt($row[28] ?? null),
                                'hasInternet' => $row[29]=="Yes" ? true : false,
                            ]);
                        }
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
