<?php

namespace App\Exports;

use App\Exports\Data\ParticipantDataExport;
use App\Exports\Data\SchoolDataExport;
use App\Exports\Input\MissionExportInput;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MissionExport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function sheets(): array
    {
        return [
            new MissionExportInput(),
            new SchoolDataExport(),
            new ParticipantDataExport()
        ];
    }
}
