<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Request;

class Mission extends Model
{
    use LogsActivity;
    protected $fillable = ['purpose', 'start_date', 'end_date', 'description', 'perspective', 'conclusion', 'appendix', 'report_uri', 'attendance_uri', 'assessment_uri', 'slide_uri'];

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
            ->logOnly(['purpose', 'start_date', 'end_date', 'description'])
            ->useLogName('បេសកម្ម')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    public function schools()
    {
        return $this->belongsToMany(School::class, 'mission_school', 'mission_id', 'school_id')
            ->select(['schools.id', 'schools.school_code', 'schools.school_name_kh', 'schools.school_name_en', 'province_en', 'province_kh', 'school_type_en', 'school_type_kh']);
    }
    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'mission_participant', 'mission_id', 'participant_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function schoolParticipants()
    {
        return $this->hasMany(SchoolParticipants::class);
    }

    public function missionSchools()
    {
        return $this->hasMany(MissionSchool::class);
    }
}
