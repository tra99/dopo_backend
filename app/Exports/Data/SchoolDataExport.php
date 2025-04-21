<?php

namespace App\Exports\Data;

use App\Models\School;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SchoolDataExport implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $school = School::select('id', 'school_code', 'school_name_kh', 'village_en', 'village_kh', 'commune_en', 'commune_kh', 'district_en', 'district_kh', 'school_name_en', 'school_type_en', 'school_type_kh', 'province_en', 'province_kh')->sortBy('school_name_kh', 'asc')->get()->toArray();
        $header = ['id', 'school_code', 'school_name_kh', 'village_en', 'village_kh', 'commune_en', 'commune_kh', 'district_en', 'district_kh', 'school_name_en', 'school_type_en', 'school_type_kh', 'province_en', 'province_kh'];
        return array_merge([$header], $school);
    }

    public function title(): string
    {
        return 'បញ្ជីសាលារៀន';
    }
}
