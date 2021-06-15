<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
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
    }
}
