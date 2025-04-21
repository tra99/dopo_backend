<?php

namespace App\Observers;

use App\Models\Answer;
use App\Models\SchoolCategory;
use Illuminate\Support\Facades\DB;

class SchoolCategoryObserver
{
    /**
     * Handle the Answer "created" event.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    public function created(Answer $answer)
    {
        $this->recalculateCategoryPoints($answer);
    }

    /**
     * Handle the Answer "updated" event.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    public function updated(Answer $answer)
    {
        $this->recalculateCategoryPoints($answer);
    }

    /**
     * Handle the Answer "deleted" event.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    public function deleted(Answer $answer)
    {
        $this->recalculateCategoryPoints($answer);
    }

    /**
     * Recalculate the total points for the category and school.
     *
     * @param  \App\Models\Answer  $answer
     * @return void
     */
    protected function recalculateCategoryPoints(Answer $answer)
    {
        // Get the category_id via the Question model (related through Answer)
        $categoryId = $answer->question->category_id;
        $schoolId = $answer->schoolSurvey->school_id;

        // Calculate the total points for this category and school
        $totalPoints = Answer::whereHas('question', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })
            ->whereHas('schoolSurvey', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->sum('point');

        // Update or create the entry in the category_school_points table
        SchoolCategory::updateOrCreate(
            ['school_id' => $schoolId, 'category_id' => $categoryId],
            ['total_score' => $totalPoints]
        );
    }
}
