<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Request;

class Evaluation extends Model
{
    use LogsActivity;
    protected $fillable = ['evaluation_criteria_id', 'school_id', 'mission_id', 'result', 'score', 'description', 'documents'];

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
            ->logOnly(['evaluation_criteria_id', 'mission_id', 'result', 'score', 'description', 'documents'])
            ->useLogName('ចំនុចផ្ទៀងផ្ទាត់')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function evaluation_criteria(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriteria::class);
    }
}
