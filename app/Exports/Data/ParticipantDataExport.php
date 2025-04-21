<?php

namespace App\Exports\Data;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ParticipantDataExport implements FromArray, WithTitle
{
    /**
     * @return array
     */
    public function array(): array
    {
        $participants = Participant::select('id', 'title', 'name', 'position', 'organization')->sortBy('created_at', 'desc')->get()->toArray();
        $header = ['id', 'title', 'name', 'position', 'organization'];

        return array_merge([$header], $participants);
    }

    public function title(): string
    {
        return 'បញ្ជីអ្នកចូលរួម';
    }
}
