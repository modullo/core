<?php


namespace App\Classes\LMS;


use App\Classes\ModulloClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\ModulesResource;
use App\Models\Lms\Courses;
use App\Models\Lms\Modules;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class ModulesClass extends ModulloClass
{

    private Courses $courses;
    private Modules $modules;

    protected array $updateFields = [
        'title' => 'title',
        'description' => 'description',
        'duration' => 'duration',
        'module_number' => 'module_number',
    ];

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

    public function updateModule(array $data,  Model $module){
        $this->updateModelAttributes($module, $data);
        $module->save();
        $resource = new ModulesResource($module);
        return response()->updated('module updated successfully', $resource, 'module');
    }

}