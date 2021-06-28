<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearnerCourses extends Model
{
    use SoftDeletes;
    protected $table = 'lms_learners_courses';

    protected $guarded = [];
}
