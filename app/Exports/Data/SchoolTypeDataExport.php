<?php

namespace App\Exports\Data;

use App\Models\SchoolType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SchoolTypeDataExport implements FromArray, WithTitle
{
    /**
     * @return array
     */
    public function array(): array
    {
        $school_type = SchoolType::select('id', 'title', 'school_type_kh', 'school_type_en')->sortBy('created_at', 'desc')->get()->toArray();
        $header = ['id', 'title', 'school_type_kh', 'school_type_en'];

        return array_merge([$header], $school_type);
    }

    public function title(): string
    {
        return 'បញ្ជីប្រភេទសាលារៀន';
    }
}
