<?php

namespace App\Observers;

use App\Models\Evaluation;
use App\Models\MissionSchool;

class MissionEvaluateOberser
{
    public function created(Evaluation $evaluation)
    {
        $this->recalulateEvaluatePoint($evaluation);
    }

    public function updated(Evaluation $evaluation)
    {
        $this->recalulateEvaluatePoint($evaluation);
    }

    public function deleted(Evaluation $evaluation)
    {
        $this->recalulateEvaluatePoint($evaluation);
    }

    protected function recalulateEvaluatePoint(Evaluation $evaluation)
    {
        $mission_id = $evaluation->mission_id;
        $school_id = $evaluation->school_id;

        $totalPoints = Evaluation::where('mission_id', $mission_id)->where('school_id', $school_id)->sum('score');

        MissionSchool::updateOrCreate(
            ['school_id' => $school_id, 'mission_id' => $mission_id],
            ['evaluation_scores' => (int) $totalPoints]
        );
    }
}
