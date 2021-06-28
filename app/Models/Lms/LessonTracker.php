<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonTracker extends Model
{

    use SoftDeletes;

    protected $table = 'lms_learners_lessons_tracker';

    protected $guarded = [];


}
