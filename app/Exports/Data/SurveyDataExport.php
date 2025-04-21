<?php

namespace App\Exports\Data;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SurveyDataExport implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    //    protected $fillable = ['title','description','is_published','is_evaluated','school_type','start_date','end_date'];


    /**
     * @return array
     */
    public function array(): array
    {
        $school_type = Survey::select('id', 'title', 'description', 'is_published', 'is_evaluated', 'school_type', 'start_date', 'end_date')->sortBy('created_at', 'desc')->get()->toArray();
        $header = ['id', 'title', 'description', 'is_published', 'is_evaluated', 'school_type', 'start_date', 'end_date'];

        return array_merge([$header], $school_type);
    }

    public function title(): string
    {
        return 'បញ្ជីស្រង់ទិន្នន័យតាមសាលា';
    }
}
