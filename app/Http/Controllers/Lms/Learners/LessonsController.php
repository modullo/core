<?php

namespace App\Http\Controllers\Lms\Learners;

use App\Classes\LMS\LessonsClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Lms\Lessons;
use App\Models\Lms\Modules;
use App\Models\Lms\Tenants;
use Illuminate\Http\Request;
use App\Models\Lms\Learners;
use Illuminate\Validation\ValidationException;
use LogicException;

class LessonsController extends Controller
{


    private LessonsClass $lessonsClass;

    private Learners $learners;


    public function __construct()
    {
        $this->lessonsClass = new LessonsClass;
        $this->learners = new Learners;
    }



    public function markComplete(Request $request, string $lessonId){
        $user = $request->user();
        $learner = $this->learners->newQuery()->where('lms_user_id',$user->id)->first();
        if(!$learner) throw new ResourceNotFoundException('the learner could not be found');

        return $this->lessonsClass->completeLesson($learner->id, $lessonId, $learner->tenant_id);
    }


}
