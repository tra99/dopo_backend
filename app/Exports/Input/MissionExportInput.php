<?php

namespace App\Exports\Input;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class MissionExportInput implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return [
            ['ល.រ', 'គោលបំណង', 'ថ្ងៃចាប់ផ្តើមបេសកកម្ម', 'ថ្ងៃបញ្ចប់បេសកកម្ម', 'សាលារៀន (id)', 'អ្នកចូលរួម​ (id)', 'បរិយាយ'],
            ['1', 'going to do something awesome', '12-12-2024', '13-12-2024', '2,3,4', '1,2,3', 'អ្វីៗអាចកើតទៅបាន កំឡុងពេលបេសកកម្មនេះ']
        ];
    }

    public function title(): string
    {
        return 'បញ្ជូលបេសកកម្មថ្មី';
    }
}
