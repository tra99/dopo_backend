<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolType extends Model
{
    protected $fillable = ['id', 'en_name', 'kh_name'];
}
