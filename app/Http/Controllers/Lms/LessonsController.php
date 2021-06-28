<?php

namespace App\Http\Controllers\Lms;

use App\Classes\LMS\LessonsClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Lms\Lessons;
use App\Models\Lms\Modules;
use App\Models\Lms\Tenants;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use LogicException;

class LessonsController extends Controller
{


    private LessonsClass $lessonsClass;
    private Lessons $lessons;
    private Modules $modules;
    private Tenants $tenants;


    public function __construct()
    {
        $this->lessonsClass = new LessonsClass;
        $this->lessons = new Lessons;
        $this->modules = new Modules;
        $this->tenants = new Tenants;
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request, string $moduleId)
    {
        $this->validate($request,
            [
                "title" => "required",
                "description" => "string|required",
                "lesson_image" => "nullable",
                "lesson_number" => 'required|numeric',
                "lesson_type" => "required|in:video,article,quiz,exercise,survey,gamification",
                "skills_gained" => "required",
                "duration" => "string|required",
                "resource_id" => "required",
            ]
        );
        $module = $this->modules->newQuery()->where('uuid', $moduleId)->first();
        if (!$module) throw new ResourceNotFoundException('unable to find module in our records');
        $check = $this->lessons->newQuery()->where(['module_id' => $module->id, 'lesson_number' => $request->lesson_number])->exists();
        if ($check) {
            throw new LogicException('the lesson number has already been taken for the module ');
        }
        $user = $request->user();
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->lessonsClass->createNewLesson($tenant->id, $request->all(), $moduleId);
    }

    public function index(Request $request, string $moduleId)
    {
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $user = $request->user();
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->lessonsClass->fetchAllLessons($tenant->id,$search, $moduleId,$request->courseId, $limit);
    }

    public function all(Request $request)
    {
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $user = $request->user();
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->lessonsClass->fetchAllLessons($tenant->id,$search, null,null, $limit);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, string $lessonId)
    {
        $this->validate($request,
            [
                "title" => "required",
                "description" => "required",
                "lesson_image" => "nullable",
                "lesson_number" => 'required|numeric',
                "lesson_type" => "required|in:video,article,quiz,exercise,survey,gamification",
                "skills_gained" => "required",
                "duration" => "string|required",
                "resource_id" => "required",
            ]
        );

        $lesson = $this->lessons->newQuery()->where('uuid', $lessonId)->first();
        if (!$lesson) throw new ResourceNotFoundException('the lesson could not be found');
        $check = $this->lessons->newQuery()->where(['module_id' => $lesson->module_id, 'lesson_number' => $request->lesson_number])->where('id', '!=', $lesson->id)->exists();
        if ($check) throw new LogicException('the lesson number has already been taken for the lesson ');
        return $this->lessonsClass->updateLesson($lessonId, $request->all());

    }

    public function single(string $lessonId)
    {
        return $this->lessonsClass->showDetails($lessonId);
    }



}
