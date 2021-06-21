<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearnerPrograms extends Model
{
    use SoftDeletes;
    protected $table = 'lms_learner_programs';

    protected $guarded = [];
}
