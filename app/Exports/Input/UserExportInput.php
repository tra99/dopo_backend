<?php

namespace App\Exports\Input;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserExportInput implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return [
            ['ល.រ', 'ឈ្មោះ', 'តួរនាទី (id)', 'អ៊ីម៉ែល', 'ពាក្យសម្ងាត់', 'សាលារៀន (id)', 'បរិយាយ (ផ្សេងៗ)'],
            ['1', 'ឆាង ឈីត', 2, 'example@gmail.com', 'Example@123', 1620, 'example']
        ];
    }

    public function title(): string
    {
        return 'បញ្ជូលឈ្មោះអ្នកប្រើប្រាស់ថ្មី';
    }
}
