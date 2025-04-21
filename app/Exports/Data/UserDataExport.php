<?php

namespace App\Exports\Data;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserDataExport implements FromArray, WithTitle
{
    /**
     * @return array
     */
    public function array(): array
    {
        $user = User::select('id', 'name', 'school_id', 'description')->sortBy('created_at', 'desc')->get()->toArray();
        $header = ['id', 'name', 'school_id', 'description'];

        return array_merge([$header], $user);
    }

    public function title(): string
    {
        return 'បញ្ជីអ្នកប្រើប្រាស់';
    }
}
