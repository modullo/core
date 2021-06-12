<?php


namespace App\Classes\Lms;


use App\Classes\ModulloClass;
use App\Models\Lms\Courses;
use App\Models\Lms\Modules;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\BinaryOp\Mod;

class ModulesClass extends ModulloClass
{
    private Courses $courses;
    private Modules $modules;
    public function __construct(){
        $this->courses = new Courses;
        $this->modules = new Modules;
    }

    public function createModule(){

        if(!$this->courses->find($courseId))
        {
            throw new ResourceNotFoundException("Course not found");
        }
        $module = $this->moduleRepo->create([
            "tenant_id" => $tenantId,
            "course_id" => $courseId,
            "title" => $title,
            "description" => $description,
            "skills_gained" => $skills_gained,
            "duration" => $duration,
            "module_number" => $module_number
        ]);

        return $this->created("Module created successfully",new ModuleResource( $module),"module");
    }

}