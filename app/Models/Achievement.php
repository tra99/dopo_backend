<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'mission_id', 'school_id', 'support_phase_id'];

    public function mission()
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function support_phase()
    {
        return $this->belongsTo(SupportPhase::class, 'support_phase_id');
    }

    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            AchieveCategoryOrSupportPhase::class,
            'achievement_id',
            'category_id'
        );
    }

    public function categoriesOrSupportPhases()
    {
        return $this->hasMany(AchieveCategoryOrSupportPhase::class, 'achievement_id')
            ->with(['category', 'supportPhase'])->whereNotNull('support_phase_id');
    }


    public function support_phases()
    {
        return $this->belongsToMany(
            SupportPhase::class,
            AchieveCategoryOrSupportPhase::class,
            'achievement_id',
            'support_phase_id'
        );  // Handle many-to-many relationship with support phases
    }
}
