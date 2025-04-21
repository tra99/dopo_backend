<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Request;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'users';
    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'status',
        'avatar',
        'lastest_login',
        'description',
        'school_id',
        'fcm_token',
        'created_at',
        'updated_at',
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
            ->useLogName('អ្នកប្រើប្រាស់')
            ->logOnly(['name', 'email', 'status', 'avatar', 'school_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Define the relationship: A User belongs to a School.
     */
    public function school()
    {
        return $this->belongsTo(School::class, 'school_id')
            ->select(['id', 'school_code', 'school_name_kh', 'school_name_en', 'school_type_kh', 'school_type_en']);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'user_id', 'role_id')
            ->select(['roles.id', 'roles.name']);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
