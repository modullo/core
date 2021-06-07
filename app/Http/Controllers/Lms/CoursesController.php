<?php

namespace App\Http\Controllers\Lms;

use App\Classes\Lms\CourseClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class CoursesController extends Controller
{
    private CourseClass $courseClass;
    public function __construct()
    {
        $this->courseClass = new CourseClass;
    }

    public function create(Request $request){
        $user = $request->user();
        $this->validate($request, [
            "title" => "required",
            "course_image" => "required",
            "duration" => "required",
            "skills_to_be_gained" => "required",
            "course_state" => "required",
            "course_video" => "nullable",
            'slug' => 'nullable|string',
            'description' => 'required|string',
            'course_level' => 'nullable|in:compulsory,elective',

        ]);

        return $this->courseClass->createCourse($request->all(),$request->programId, $user);
    }
}
