<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AchieveCategoryOrSupportPhase extends Model
{
    protected $table = "achievement_details";
    protected $fillable = ['id', 'achievement_id', 'category_id', 'support_phase_id', 'description', 'score'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Define the relationship with SupportPhase
    public function supportPhase()
    {
        return $this->belongsTo(SupportPhase::class, 'support_phase_id');
    }

    public function achievement()
    {
        return $this->belongsTo(Achievement::class, 'achievement_id');
    }
}
