<?php


namespace App\Classes\LMS;


use App\Classes\ModulloClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\ModulesResource;
use App\Models\Lms\Courses;
use App\Models\Lms\Modules;
use App\Models\Lms\Tenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class ModulesClass extends ModulloClass
{

    private Courses $courses;
    private Modules $modules;
    private Tenants $tenants;

    protected array $updateFields = [
        'title' => 'title',
        'description' => 'description',
        'duration' => 'duration',
        'module_number' => 'module_number',
    ];

    public function __construct(){
        $this->courses = new Courses;
        $this->modules = new Modules;
        $this->tenants = new Tenants;
    }


    public function fetchAllModules(object $user,string $search,?string $courseId = null, int $limit = 100){
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant){
            throw new ResourceNotFoundException('unfortunately the tenant could not be found');
        }
            $builder = $this->modules->newQuery();
            if ($courseId){
                $course = $this->courses->newQuery()->where('uuid',$courseId)->first();
                if (!$course) throw new ResourceNotFoundException('could not find the given course');
                $builder = $builder->where('course_id',$course->id);
            }

            $builder = $builder
                ->where('tenant_id',$tenant->id)
                ->oldest('created_at')
                ->orderBy('module_number')
                ->paginate($limit);
            $resource = ModulesResource::collection($builder);
            return response()->fetch('modules fetched successfully',$resource,'modules');
    }

    public function createModule(object $user, array $data, object $course){
        $tenant = $this->tenants->newQuery()->where('lms_user_id', $user->id)->first();
        if (!$tenant) {
            throw new ResourceNotFoundException('unfortunately the tenant could not found');
        }
        $module = $this->modules->newQuery()->create([
            "tenant_id" => $tenant->id,
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

    public function showModule(string $moduleId)
    {
        $filter = $this->modules->newQuery()->where('uuid',$moduleId);
        $module = $filter->with('course')->first();
        if ($module) {
            $resource = new ModulesResource($module);
            return response()->fetch("module fetched successfully", $resource, "module");
        } else {
            throw new ResourceNotFoundException("Module not found");
        }
    }

}