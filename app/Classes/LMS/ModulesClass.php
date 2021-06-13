<?php


namespace App\Classes\LMS;


use App\Classes\ModulloClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\ModulesResource;
use App\Models\Lms\Courses;
use App\Models\Lms\Modules;

class ModulesClass extends ModulloClass
{
    private Courses $courses;
    private Modules $modules;
    public function __construct(){
        $this->courses = new Courses;
        $this->modules = new Modules;
    }

    public function createModule(array $data, object $course){
        $module = $this->modules->newQuery()->create([
            "course_id" => $course->id,
            "title" => $data['title'],
            "description" => $data['description'],
            "duration" => $data['duration'],
            "module_number" => $data['module_number']
        ]);

        return response()->created("Module created successfully",new ModulesResource( $module),"module");
    }

}