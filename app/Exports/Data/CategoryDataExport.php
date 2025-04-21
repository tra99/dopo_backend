<?php

namespace App\Exports\Data;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class CategoryDataExport implements FromArray, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    //$fillable = ['title', 'parent_id', 'type', 'school_type_id', 'status'];


    /**
     * @return array
     */
    public function array(): array
    {
        $school_type = Category::select('id', 'title', 'parent_id', 'type', 'school_type_id', 'status')->sortBy('created_at', 'desc')->get()->toArray();
        $header = ['id', 'title', 'parent_id', 'type', 'school_type_id', 'status'];

        return array_merge([$header], $school_type);
    }

    public function title(): string
    {
        return 'បញ្ជីសូចនករ និងស្ដង់ដារ';
    }
}
