<?php

namespace App\Models;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends SpatieActivity
{

    protected $fillable = [
        'causer_id',
        'log_name',
        'description',
        'subject_id',
        'subject_type',
        'properties',
        'event',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'batch_uuid',
        'causer_type',
        // 'causer_id',
        'subject_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'causer_id')->select(['id', 'name']);
    }
}
