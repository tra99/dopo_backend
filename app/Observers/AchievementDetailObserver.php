<?php

namespace App\Observers;

use App\Models\AchieveCategoryOrSupportPhase;
use App\Models\Evaluation;

class AchievementDetailObserver
{
    public function created(Evaluation $evaluation)
    {
        $this->recalulateAchievementDetailPoint($evaluation);
    }

    public function updated(Evaluation $evaluation)
    {
        $this->recalulateAchievementDetailPoint($evaluation);
    }

    public function deleted(Evaluation $evaluation)
    {
        $this->recalulateAchievementDetailPoint($evaluation);
    }

    protected function recalulateAchievementDetailPoint(Evaluation $evaluation)
    {
        $mission_id = $evaluation->mission_id;
        $school_id = $evaluation->school_id;
        $evaluation->load('evaluation_criteria.question');

        $category_id = $evaluation->evaluation_criteria->question->category_id;



        // Calculate the total points by summing the scores, considering the additional condition on category_id
        $totalPoints = Evaluation::where('mission_id', $mission_id)
            ->where('school_id', $school_id)
            ->whereHas('evaluation_criteria.question', function ($query) use ($category_id) {
                $query->where('category_id', $category_id);  // Filter by category_id
            })
            ->sum('score');

        // Use join to correctly reference the `achievement` table
        $achievement_detail = AchieveCategoryOrSupportPhase::with('achievement')
            ->join('achievements', 'achievements.id', '=', 'achievement_details.achievement_id')
            ->where('achievements.mission_id', $mission_id)
            ->where('achievements.school_id', $school_id)
            ->where('category_id', $category_id)
            ->first();

        if ($achievement_detail) {
            $achievement_detail->update(['score' => $totalPoints]);
        }
    }
}
