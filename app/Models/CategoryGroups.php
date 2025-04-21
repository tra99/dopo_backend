<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryGroups extends Model
{
    protected $fillable = ['id', 'title', 'school_type_kh', 'school_type_en'];

    public function categories()
    {
        return $this->hasMany(Category::class, 'school_type_id');
    }

    public function questions()
    {
        return $this->hasManyThrough(
            Question::class,
            Category::class,
            'school_type_id',
            'category_id',
            'id',
            'id'
        );
    }

}
