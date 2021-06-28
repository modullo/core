<?php


  namespace App\Classes\Lms;


  use App\Classes\ModulloClass;
  use App\Exceptions\ResourceNotFoundException;
  use App\Http\Resources\Lms\LessonResource;
  use App\Models\Lms\Assets;
  use App\Models\Lms\Courses;
  use App\Models\Lms\LearnerCourses;
  use App\Models\Lms\Learners;
  use App\Models\Lms\Lessons;
  use App\Models\Lms\LessonTracker;
  use App\Models\Lms\Modules;
  use Carbon\Carbon;
  use App\Models\Lms\Quiz;
  use App\Models\Lms\Tenants;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Support\Facades\DB;
  use LogicException;
  use function response;

  class LessonsClass extends ModulloClass
  {
    protected array $updateFields = [
      "title" => "title",
      "description" => "description",
      "lesson_image" => "lesson_image",
      "lesson_number" => 'lesson_number',
      "lesson_type" => "lesson_type",
      "skills_gained" => "skills_gained",
      "duration" => "duration",
      "resource_id" => "resource_id",
    ];
    private Lessons $lessons;
    private Courses $courses;
    private Modules $modules;
    private Tenants $tenants;
    private Assets $assets;
    private Quiz $quiz;
    private LessonTracker $lessonTracker;
    private Learners $learners;
    private LearnerCourses $learnerCourses;

    public function __construct()
    {
      $this->quiz = new Quiz;
      $this->assets = new Assets;
      $this->lessons = new Lessons;
      $this->modules = new Modules;
      $this->courses = new Courses;
      $this->tenants = new Tenants;
      $this->lessonTracker = new LessonTracker;
      $this->learners = new Learners;
      $this->learnerCourses = new LearnerCourses;
    }


    public function fetchAllLessons(string $tenantId, string $search, ?string $moduleId = null, ?string $courseId = null, int $limit = 100)
    {
      $tenant = $this->tenants->newQuery()->where('id', $tenantId)->first();
      if (!$tenant) {
        throw new ResourceNotFoundException('unfortunately the tenant could not be found');
      }
      $builder = $this->lessons->newQuery();
      if ($moduleId) {
        $module = $this->modules->newQuery()->where('uuid', $moduleId)->first();
        if (!$module) throw new ResourceNotFoundException('could not find the given module');
        $builder = $builder->where('module_id', $module->id);
      }
      if ($courseId) {
        $course = $this->modules->newQuery()->where('uuid', $courseId)->first();
        if (!$course) throw new ResourceNotFoundException('could not find the given course');
        $builder = $builder->where('course_id', $course->id);
      }

      $builder = $builder
        ->where('tenant_id', $tenant->id)
        ->oldest('created_at')
        ->orderBy('lesson_number')
        ->paginate($limit);
      $resource = LessonResource::collection($builder);
      return response()->fetch('lessons fetched successfully', $resource, 'lessons');
    }

    public function createNewLesson(string $tenantId, array $data, string $moduleId)
    {

      $module = $this->modules->newQuery()->where('uuid', $moduleId)->first();
      $tenant = $this->tenants->newQuery()->where('id', $tenantId)->first();
      if (!$tenant) {
        throw new ResourceNotFoundException('unfortunately the tenant could not be found');
      }
      if (!$module) {
        throw new ResourceNotFoundException("module not found");
      }
      $course = $this->courses->newQuery()->where('id', $module->course_id)->first();
      if (!$course) {
        throw new ResourceNotFoundException("no course found or created for the lessons module");
      }
      switch ($data['lesson_type']) {
        case 'video':
          $resource = $this->assets->where(['type' => 'video', 'uuid' => $data['resource_id']])->first();
          break;
        case 'quiz':
          $resource = $this->quiz->where(['uuid' => $data['resource_id']])->first();
          break;
        default:
          $resource = null;
          break;
      }
      if (!$resource) throw new LogicException("could not find resource for the " . $data['lesson_type'] . ' lesson type');
      $lesson = null;
      DB::transaction(function () use (
        $tenant,
        $resource,
        &$lesson,
        $module,
        $course,
        $data
      ) {
        $lesson = $this->lessons->newQuery()->create([
          "tenant_id" => $tenant->id,
          "course_id" => $course->id,
          "module_id" => $module->id,
          "title" => $data['title'],
          "description" => $data['description'],
          "lesson_image" => $data['image'] ?? null,
          "lesson_type" => trim($data['lesson_type']),
          "skills_gained" => $data['skills_gained'] ?? null,
          "duration" => $data['duration'],
          "resource_id" => $resource->id,
          "additional_resources" => isset($data['additional_resources']) ? json_encode($data['additional_resources']) : null,
          "lesson_number" => $data['lesson_number']
        ]);

      });
      $resource = new LessonResource($lesson);
      return response()->created("Lesson created successfully", $resource, "lesson");
    }

    public function showDetails(string $lessonId)
    {
      $lesson = $this->lessons->newQuery()->where('uuid', $lessonId)->first();
      if (!$lesson) throw new ResourceNotFoundException('unable to find the lesson in our records');
      $resource = new LessonResource($lesson);
      return response()->fetch('lesson created successfully', $resource, 'lesson');

    }

    public function updateLesson(string $lessonId, array $data)
    {
      $lesson = $this->lessons->newQuery()->where('uuid', $lessonId)->first();
      if (!$lesson) throw new ResourceNotFoundException('the lesson could not be found');
      if (isset($data['lesson_type'])) {
        switch ($data['lesson_type']) {
          case 'video':
            $resource = $this->assets->where(['type' => 'video', 'uuid' => $data['resource_id']])->first();
            break;
          case 'quiz':
            $resource = $this->quiz->where(['uuid' => $data['resource_id']])->first();
            break;
          default:
            $resource = null;
            break;
        }
        if (!$resource) throw new LogicException("could not find resource for the " . $data['lesson_type'] . ' lesson type');
        $data['resource_id'] = $resource->id;
      }
      $this->updateModelAttributes($lesson, $data);
      $lesson->save();
      $resource = new LessonResource($lesson);
      return response()->updated('lesson updated successfully', $resource, 'lesson');

    }

    public function completeLesson(string $learnerId, string $lessonId, string $tenantId)
    {
      $lesson = null;
      DB::transaction(function () use (&$lesson, $learnerId, $lessonId,$tenantId) {
        $lesson = $this->lessons->newQuery()->where('uuid', $lessonId)
        ->where('tenant_id',$tenantId)
        ->first();
        if (!$lesson) throw new ResourceNotFoundException("Lesson not found");
        $learner = $this->learners->newQuery()->where('id', $learnerId)->first();
        $tracker = $this->lessonTracker->newQuery()->where(function ($query) use ($lesson, $learner) {
          $query
            ->where('course_id', $lesson->course_id)
            ->where('learner_id', $learner->id)
            ->where('lesson_id', $lesson->id);

        })->exists();
        if (!$tracker) {
          $this->lessonTracker->newQuery()
            ->sharedLock()
            ->updateOrCreate([
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'course_id' => $lesson->course_id
          ], [
            'learner_id' => $learner->id,
            'lesson_id' => $lesson->id,
            'course_id' => $lesson->course_id,
            'status' => true,
            'completion_time' => Carbon::now()
          ]);

        }
        $this->calculateCourseProgress($lesson, $learner);

      });
      $resource = new LessonResource($lesson);
      return response()->created('lesson completed successfully', $resource, 'lesson');


    }

    private function calculateCourseProgress(Model $lesson, Model $learner): void
    {
      $progress = 0;
      $allCourseLessons = $this->lessons->newQuery()->where('course_id', $lesson->course_id)->count();
      $completedLessonCount = $this->lessonTracker->newQuery()
      ->where('course_id', $lesson->course_id)
      ->where('learner_id', $learner->id)
      ->count();
      if (!$completedLessonCount < 1) $progress = (int)(($completedLessonCount / $allCourseLessons) * 100);
      $this->learnerCourses->newQuery()
        ->where('course_id', $lesson->course_id)
        ->where('learner_id', $learner->id)
        ->sharedLock()
        ->update([
          'progress' => $progress
        ]);
    }

    private function checkForEndOfCourse()
    {

    }

  }