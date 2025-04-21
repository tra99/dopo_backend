<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = ['id', 'school_id', 'mission_id', 'category_id', 'challenge', 'solution'];

    public function school()
    {
        return $this->belongsTo(School::class)->select('id', 'school_code', 'school_name_kh', 'school_name_en');
    }

    public function mission()
    {
        return $this->belongsTo(Mission::class)->select(['id', 'purpose']);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->select(['id', 'title', 'parent_id']);
    }
}
