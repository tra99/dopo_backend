<?php

namespace App\Exports\Data;

use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserRoleDataExport implements FromArray, WithTitle
{
    /**
     * @return array
     */
    public function array(): array
    {
        $roles = Role::select('id', 'name')->sortBy('id', 'asc')->get()->toArray();
        $header = ['id', 'name'];

        return array_merge([$header], $roles);
    }

    public function title(): string
    {
        return 'តួនាទីរបស់អ្នកប្រើប្រាស់ក្នុងប្រព័ន្ន';
    }
}
