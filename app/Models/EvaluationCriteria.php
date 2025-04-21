<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationCriteria extends Model
{
    protected $fillable = ['title', 'options', 'question_id'];

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
