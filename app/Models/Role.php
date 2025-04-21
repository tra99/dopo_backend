<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\Models\Activity;

class Role extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'status',
        'created_at',
    ];

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent')
        ]);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'status'])
            ->logOnlyDirty()
            ->useLogName('តួរនាទី')
            ->dontSubmitEmptyLogs()
        ;
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'role_id', 'user_id')
            ->withTimestamps();
    }
}
