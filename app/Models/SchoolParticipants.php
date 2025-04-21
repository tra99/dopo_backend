<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolParticipants extends Model
{
    protected $fillable = [
        'school_id',
        'mission_id',
        'organization',
        'number_of_male',
        'number_of_female',
        'file_uri',
    ];

    public function school()
    {
        return $this->belongsTo(School::class)->select('id', 'school_code', 'school_name_kh', 'school_name_en');
    }
}
