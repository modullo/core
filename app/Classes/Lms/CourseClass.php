<?php


namespace App\Classes\Lms;


use App\Http\Resources\Lms\CourseResource;
use App\Models\Lms\Courses;
use App\Models\Lms\Programs;
use App\Models\Lms\Tenants;
use Illuminate\Support\Facades\DB;

class CourseClass
{
    protected Courses $courses;
    protected Programs $programs;
    protected Tenants $tenants;

    public function __construct()
    {
        $this->courses = new Courses;
        $this->programs = new Programs;
        $this->tenants = new Tenants;
    }

    protected array $updateFields = [
        'title' => 'title',
        'description' => 'description',
        'course_image' => 'image',
        'duration' => 'video_overview',
        'skills_to_be_gained' => 'skills_to_be_gained',
        'course_state' => 'course_state',
        'price' => 'price',
    ];

    public function createCourse(array $data, string $programId, object $user)
    {
        $course = null;
        $tenant = $this->tenants->newQuery()->where('lms_user_id', $user->id)->first();
        if (!$tenant) {
            throw new ResourceNotFoundException('unfortunately the tenant could not found');
        }
        $program = $this->programs->newQuery()->where('uuid', $programId)->first();
        if (!$program) {
            throw new ResourceNotFoundException('unfortunately the program could not found');
        }
        [
            'title' => $title, 'description' => $description, 'course_image' => $course_image,
            'duration' => $duration, 'skills_to_be_gained' => $skills_to_be_gained,
            'course_state' => $course_state,
        ] = $data;
        DB::transaction(function () use (
            &$course,
            $data, $program, $tenant, $title, $description,
            $course_image, $duration, $skills_to_be_gained,
            $course_state
        )
        {
            $course = $this->courses->newQuery()->create([
                "tenant_id" => $tenant->id,
                "program_id" => $program->id,
                "title" => $title,
                "description" => $description,
                "course_image" => $course_image,
                "duration" => $duration,
                "skills_to_be_gained" => $skills_to_be_gained,
                "course_state" => $course_state,
            ]);
        });

        return response()->created('Course created successfully', new CourseResource($course), "course");
    }


    public function showCourse(string $courseId, object $user)
    {
        $tenant = $this->tenants->newQuery()->where('lms_user_id', $user->id)->first();
        if (!$tenant) {
            throw new ResourceNotFoundException('unfortunately the tenant could not found');
        }
        $filter = $this->courses->newQuery()->where('tenant_id',$tenant->id)->whereId($courseId);
        $course = $filter->first();
        if ($course) {
            $resource = new CourseResource($course);
            return  response()->fetch("course fetched successfully", $resource, "course");
        } else {
            throw new ResourceNotFoundException("Course not found");
        }
    }

}