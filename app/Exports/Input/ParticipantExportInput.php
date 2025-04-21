<?php

namespace App\Exports\Input;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ParticipantExportInput implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        return [
            ['ល.រ', 'ងារ', 'ឈ្មោះ', 'តួរនាទី', 'អ៊ីម៉ែល', 'អង្គភាព', 'បរិយាយ (ផ្សេងៗ)'],
            ['1', 'លោក', 'ឆាង ឈីត', 'គ្រូសាលាអនុវិទ្យាល័យ', 'example@gmail.com', 'Moeys', 'example']
        ];
    }

    public function title(): string
    {
        return 'បញ្ជូលឈ្មោះអ្នកចូលរួមសម្រាប់បេសកម្មថ្មី';
    }
}
