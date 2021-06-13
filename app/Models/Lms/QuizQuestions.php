<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class QuizQuestions extends Model
{
    protected $table = 'lms_quiz_questions';

    protected $guarded = [];

    public function quiz(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}
