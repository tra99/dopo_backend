<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Request;

class Question extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = ['id', 'category_id', 'question', 'question_type', 'answer_option', 'rule', 'description', 'published_at', 'school_type', 'created_at', 'updated_at'];
    protected $guarded = ['id'];

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
            ->logOnly(['category_id', 'question', 'question_type', 'answer_option', 'rule', 'description', 'published_at', 'school_type'])
            ->useLogName('សំណួរ')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')
            ->select(['id', 'title', 'parent_id'])
            ->with('parent')
        ;
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)
            ->select('id', 'answer', 'point', 'question_id', 'user_id'); // Specify the columns you want to select
    }

    public function evaluation_criterias()
    {
        return $this->hasMany(EvaluationCriteria::class);
    }
}
