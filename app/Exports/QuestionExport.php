<?php

namespace App\Exports;

use App\Exports\Data\SchoolDataExport;
use App\Exports\Data\UserRoleDataExport;
use App\Exports\Input\UserExportInput;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserExport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function sheets(): array
    {
        return [
            new UserExportInput(),
            new UserRoleDataExport(),
            new SchoolDataExport(),
        ];
    }
}
