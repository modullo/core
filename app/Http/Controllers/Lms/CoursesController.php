<?php

namespace App\Http\Controllers\Lms;

use App\Classes\LMS\CourseClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CoursesController extends Controller
{
    private CourseClass $courseClass;
    public function __construct()
    {
        $this->courseClass = new CourseClass;
    }

    public function index(Request $request,string $programId){
        $user = $request->user();
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        return $this->courseClass->fetchAllCourses($search, $user,$programId,'all',$limit);
    }
    public function all(Request $request){
        $user = $request->user();
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        return $this->courseClass->fetchAllCourses($search, $user,null,'all',$limit);
    }
    public function create(Request $request){
        $user = $request->user();
        $this->validate($request, [
            "title" => "required",
            "course_image" => "required",
            "duration" => "required|string",
            "skills_to_be_gained" => "required",
            "course_state" => "required|in:draft,published",
            "course_video" => "nullable",
            'slug' => 'nullable|string',
            'description' => 'required|string',
            'course_level' => 'nullable|in:compulsory,elective',

        ]);

        return $this->courseClass->createCourse($request->all(),$request->programId, $user);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, string $courseId){
        $this->validate($request, [
            "title" => "nullable|string",
            "course_image" => "nullable|string",
            "duration" => "nullable|string",
            "skills_to_be_gained" => "nullable",
            "course_state" => "nullable|in:draft,published",
            "course_video" => "nullable",
            'slug' => 'nullable|string',
            'description' => 'nullable|string',
            'course_level' => 'nullable|in:compulsory,elective',

        ]);

        return $this->courseClass->updateCourse($request->courseId,$request->all());
    }
    public function single(Request $request,string $courseId){
        $user = $request->user();

        return $this->courseClass->showCourse($courseId,$user);
    }
}
