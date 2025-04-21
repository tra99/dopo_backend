<?php

namespace App\Exports\Input;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class SurveyExportInput implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return [
            ['ល.រ', 'សំណួរ', 'ថ្ងៃចាប់ផ្ដើម', 'ថ្ងៃបញ្ចប់', 'ប្រភេទសាលារៀន (english)', 'បរិយាយ (ផ្សេងៗ)'],
            ['1', 'ស្រង់បតិតាមសាលារៀនលើកទី១', '12-12-2024', '13-12-2024', 'Lower Secondary School', 'example']
        ];
    }

    public function title(): string
    {
        return 'បញ្ជីបញ្ជូលការស្រង់បតិតាមសាលារៀន';
    }
}
