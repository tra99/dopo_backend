<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Request;

class Participant extends Model
{
    use LogsActivity;
    protected $fillable = ['name', 'organization', 'phone', 'email', 'user_id', 'title', 'position', 'telegram', 'address', 'avatar'];

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
            ->logOnly(['name', 'organization', 'phone', 'email', 'user_id', 'title', 'position', 'telegram', 'address', 'avatar'])
            ->useLogName('អ្នកចូលរួមបេសកម្ម')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function missions()
    {
        return $this->belongsToMany(Mission::class, 'mission_participant', 'participant_id', 'mission_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
