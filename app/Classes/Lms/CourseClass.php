<?php


namespace App\Classes\Lms;


use App\Classes\ModulloClass;
use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\CourseResource;
use App\Models\Lms\Courses;
use App\Models\Lms\Programs;
use App\Models\Lms\Tenants;
use Hostville\Modullo\Services\Courses\Course;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class CourseClass extends ModulloClass
{
    protected Courses $courses;
    protected Programs $programs;
    protected Tenants $tenants;
    protected array $updateFields = [
        'title' => 'title',
        'description' => 'description',
        'course_image' => 'image',
        'duration' => 'video_overview',
        'skills_to_be_gained' => 'skills_to_be_gained',
        'course_state' => 'course_state',
        'price' => 'price',
    ];

    public function __construct()
    {
        $this->courses = new Courses;
        $this->programs = new Programs;
        $this->tenants = new Tenants;
    }

    /**
     * @param object $user
     * @param string|null $programId
     * @param string|null $course_state
     */
    public function fetchAllCourses(string $search,object $user,?string $programId = null, ?string $course_state = 'all',int $limit = 100)
    {
        $builder = $this->courses->newQuery();
        if ($programId){
            $program = $this->programs->newQuery()->where('uuid',$programId)->firstOrFail();
            $builder = $builder->where('program_id',$program->id);
        }
        $builder->where('tenant_id',$user->id);
        switch ($course_state){
            case 'publish':
                $filter->where('course_state','publish');
                break;
            case 'draft':
                $filter->where('course_state','draft');
                break;
            case 'all':
            default:
                break;
        }
        $builder = $builder
            ->oldest('created_at')
            ->paginate($limit);
        $resource = CourseResource::collection($builder);
        return response()->fetch('courses fetched successfully',$resource,'courses');
    }

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
        ) {
            $slug = str_slug($title,'-');
            $course = $this->courses->newQuery()->create([
                "tenant_id" => $tenant->id,
                "program_id" => $program->id,
                'slug' => $slug,
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
        $filter = $this->courses->newQuery()->where('tenant_id', $tenant->id)->whereId($courseId);
        $course = $filter->first();
        if ($course) {
            $resource = new CourseResource($course);
            return response()->fetch("course fetched successfully", $resource, "course");
        } else {
            throw new ResourceNotFoundException("Course not found");
        }
    }

    public function updateCourse(string $courseId, array $data)
    {
        $course = $this->courses->newQuery()->where('uuid', $courseId)->first();
        if ($course === null) {
            throw new NotFoundResourceException('unfortunately we could not find the given course');
        }
        $this->updateModelAttributes($course, $data);
        $course->save();

        $course = new CourseResource($course);
        return response()->updated('course updated successfully', $course, 'course');

    }


}