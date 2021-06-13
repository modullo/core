<?php

namespace App\Http\Controllers\Lms;

use App\Classes\LMS\ModulesClass;
use App\Exceptions\CustomValidationFailed;
use App\Http\Controllers\Controller;
use App\Models\Lms\Courses;
use App\Models\Lms\Modules;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ModulesController extends Controller
{
    private ModulesClass $modulesClass;
    private Courses $courses;

    public function __construct(){
        $this->modulesClass = new ModulesClass;
        $this->courses = new Courses;
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request, string $courseId){
        $this->validate($request,[
            "title" => "required",
            "description" => "required",
            "duration" => "required",
            "module_number" => "required|numeric"
        ]);
        $course = $this->courses->newQuery()->where('uuid',$courseId)->first();
        if(!$course)
        {
            throw new ResourceNotFoundException("Course not found");
        }
        $check = Modules::where(['course_id' => $course->id,'module_number'=>$request->module_number])->exists();
        if($check){
            throw new CustomValidationFailed('the module number has already been taken for the course');
        }

        return $this->modulesClass->createModule($request->all(),$course);
    }


}
