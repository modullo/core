<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $table = 'lms_quiz';

    protected $guarded = [];

    public function questions(){
        return $this->hasMany(QuizQuestions::class)->orderBy('question_number');
    }
}
