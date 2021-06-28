<?php

namespace App\Http\Resources\Lms;

use App\Models\Lms\Learners;
use App\Models\Lms\LessonTracker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */

    protected LessonTracker $lessonTracker;
    protected Learners $learners;

  /**
   * LessonResource constructor.
   */

  public function __construct($resource)
  {
    parent::__construct($resource);
    $this->lessonTracker = new LessonTracker;
    $this->learners = new Learners;
  }

  public function toArray($request): array
    {
      $user = $request->user();
        $response =  [
            "id" => $this->uuid,
            "module_id" => new ModulesResource($this->whenLoaded('module')),
            "title" => $this->title,
            "description" => $this->description,
            "lesson_image" => $this->lesson_image,
            "lesson_number" => $this->lesson_number,
            "lesson_type" => $this->lesson_type,
            "skills_gained" => $this->skills_gained,
            "duration" => $this->duration,
            'lesson_resource' => $this->when(true,function (){
               switch ($this->lesson_type){
                   case 'video':
                       return new AssetResource($this->assets);
                   case 'quiz':
                       return new QuizResource($this->quiz);
                   default:
                       return null;
               }
            }),
            'created_at' => (string) $this->created_at->toIso8601String(),
            'updated_at' => (string) $this->updated_at->toIso8601String(),
        ];

        if ($user->getRoleNames()[0] === 'lms_learner'){
          $mergers = [
            'completed' => $this->checkCompletion($user)
          ];
          $response = array_merge($response,$mergers);
        }

        return $response;
    }

    protected function checkCompletion(object $user): bool
    {
      $learnerId = $user->learner->id;
      return $this->lessonTracker->newQuery()
        ->where('lesson_id',$this->id)
        ->where('learner_id',$learnerId)
        ->exists();
    }
}
