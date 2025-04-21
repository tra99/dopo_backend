<?php

namespace App\Exports;

use App\Exports\Data\SchoolTypeDataExport;
use App\Exports\Input\SurveyExportInput;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SurveyExport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function sheets(): array
    {
        return [
            new SurveyExportInput(),
            new SchoolTypeDataExport(),
        ];
    }
}
