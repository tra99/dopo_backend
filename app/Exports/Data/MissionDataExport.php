<?php

namespace App\Exports\Data;

use App\Models\Mission;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class MissionDataExport implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    //$    protected $fillable = ['purpose', 'start_date', 'end_date', 'description', 'perspective', 'conclusion', 'appendix', 'report_uri', 'attendance_uri', 'assessment_uri', 'slide_uri'];


    /**
     * @return array
     */
    public function array(): array
    {
        $school_type = Mission::select('id', 'purpose', 'start_date', 'end_date', 'description')->sortBy('created_at', 'desc')->get()->toArray();
        $header = ['id', 'purpose', 'start_date', 'end_date', 'description'];

        return array_merge([$header], $school_type);
    }

    public function title(): string
    {
        return 'បញ្ជីបេសកកម្ម';
    }
}
