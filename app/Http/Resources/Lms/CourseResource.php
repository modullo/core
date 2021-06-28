<?php

namespace App\Http\Resources\Lms;

use App\Models\Lms\LearnerCourses;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
  protected LearnerCourses $learnerCourses;

  public function __construct($resource)
  {
    parent::__construct($resource);

    $this->learnerCourses = new LearnerCourses;
  }

  /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
      $user = $request->user();
        $response  =  [
            'id' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            "tenant" => new ProgramsResource($this->whenLoaded('tenant')),
            "program" => new ProgramsResource($this->whenLoaded('program')),
            'modules' =>  ModulesResource::collection($this->whenLoaded('modules')),
            "description" => $this->description,
            "course_image" => $this->course_image,
            "duration" => $this->duration,
            "skills_to_be_gained" => $this->skills_to_be_gained,
            "course_state" => $this->course_state,
            "html_formatted_description" => $this->html_formatted_description ?? '',
            "short_description" => $this->short_description ?? '',
            "course_level" => $this->course_level,
            "course_requirements" => $this->course_requirements,
            'created_at' => (string) $this->created_at->toIso8601String(),
            'updated_at' => (string) $this->updated_at->toIso8601String(),
        ];


      if ($user->getRoleNames()[0] === 'lms_learner'){
        $mergers = [
          'progress' => $this->getCourseProgress($user)
        ];
        $response = array_merge($response,$mergers);
      }

        return $response;
    }

    protected function getCourseProgress(object $user){
      $learnerId = $user->learner->id;
      return $this->learnerCourses->newQuery()->select('progress')->where('course_id',$this->id)
      ->where('learner_id',$learnerId)->first()->progress;
    }
}
