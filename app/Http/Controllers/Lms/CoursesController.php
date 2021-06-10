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
            "duration" => "required",
            "skills_to_be_gained" => "required",
            "course_state" => "required|in:draft,published",
            "course_video" => "nullable",
            'slug' => 'nullable|string',
            'description' => 'required|string',
            'course_level' => 'nullable|in:compulsory,elective',

        ]);

        return $this->courseClass->createCourse($request->all(),$request->programId, $user);
    }
}
