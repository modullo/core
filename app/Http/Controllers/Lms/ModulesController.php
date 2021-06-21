<?php

namespace App\Http\Controllers\Lms;

use App\Classes\LMS\ModulesClass;
use App\Exceptions\CustomValidationFailed;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Lms\Courses;
use App\Models\Lms\Modules;
use App\Models\Lms\Tenants;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ModulesController extends Controller
{
    private ModulesClass $modulesClass;
    private Courses $courses;
    private Modules $modules;
    private Tenants $tenants;

    public function __construct()
    {
        $this->modulesClass = new ModulesClass;
        $this->courses = new Courses;
        $this->modules = new Modules;
        $this->tenants = new Tenants;
    }

    public function index(Request $request,string $courseId){
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $user = $request->user();
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->modulesClass->fetchAllModules($tenant->id,$search,$courseId,$limit);
    }

    public function all(Request $request){
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $user = $request->user();
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->modulesClass->fetchAllModules($tenant->id,$search,null,$limit);
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request, string $courseId)
    {
        $this->validate($request, [
            "title" => "required",
            "description" => "required",
            "duration" => "required",
            "module_number" => "required|numeric"
        ]);
        $course = $this->courses->newQuery()->where('uuid', $courseId)->first();
        if (!$course) {
            throw new ResourceNotFoundException("Course not found");
        }
        $check = Modules::where(['course_id' => $course->id, 'module_number' => $request->module_number])->exists();
        if ($check) {
            throw new CustomValidationFailed('the module number has already been taken for the course');
        }
        $user = $request->user();
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->modulesClass->createModule($tenant->id,$request->all(), $course);
    }


    /**
     * @throws ValidationException
     */
    public function update(Request $request, string $moduleId)
    {
        $this->validate($request, [
            "title" => "nullable",
            "description" => "nullable",
            "duration" => "nullable",
            "module_number" => "required|numeric"
        ]);
        $module = $this->modules->newQuery()->where('uuid', $moduleId)->first();
        if ($module === null) {
            throw new NotFoundResourceException('the module could not be found');
        }
        $check = $this->modules->newQuery()->where(['course_id' => $module->course_id, 'module_number' => $request->module_number])->where('id', '!=', $module->id)->exists();
        if ($check) {
            throw new CustomValidationFailed('the module number has already been chosen for another module for the current modules course');
        }
        return $this->modulesClass->updateModule($request->all(),$module);
    }

    public function single(string $moduleId){
        return $this->modulesClass->showModule($moduleId);
    }


}
