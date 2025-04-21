<?php

namespace App\Observers;

use App\Models\Answer;
use App\Models\SchoolSurvey;
use Illuminate\Support\Facades\DB;

class SchoolSurveyObserver
{
    /**
     * Handle the Answer "created" event.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    public function created(Answer $answer)
    {
        $this->recalculatePoints($answer);
    }

    /**
     * Handle the Answer "updated" event.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    public function updated(Answer $answer)
    {
        $this->recalculatePoints($answer);
    }

    /**
     * Handle the Answer "deleted" event.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    public function deleted(Answer $answer)
    {
        $this->recalculatePoints($answer);
    }

    /**
     * Recalculate the points for the associated SchoolSurvey.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    private function recalculatePoints(Answer $answer)
    {
        // Recalculate points for the associated SchoolSurvey
        $schoolSurvey = $answer->schoolSurvey;

        if ($schoolSurvey) {
            // Example logic to calculate the points (sum of points of related answers)
            $points = DB::table('answers')
                ->where('school_survey_id', $schoolSurvey->id)
                ->sum('point');
            // Update the SchoolSurvey with the new points
            $schoolSurvey->score = $points;
            $schoolSurvey->save();
        }
    }
}
