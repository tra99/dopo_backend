<?php

namespace App\Exports;

use App\Exports\Data\UserDataExport;
use App\Exports\Input\ParticipantExportInput;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ParticipantExport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function sheets(): array
    {
        return [
            new ParticipantExportInput(),
            new UserDataExport(),
        ];
    }
}
