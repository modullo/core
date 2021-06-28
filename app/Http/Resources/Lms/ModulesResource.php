<?php

namespace App\Http\Resources\Lms;

use App\Http\Resources\Lms\LessonResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ModulesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->uuid,
            "course" => new CourseResource($this->whenLoaded('course')),
            "lessons" => LessonResource::collection($this->whenLoaded('lessons')),
            "title" => $this->title,
            "description" => $this->description,
            "module_number" => $this->module_number,
            "duration" => $this->duration,
            'created_at' => (string) $this->created_at->toIso8601String(),
            'updated_at' => (string) $this->updated_at->toIso8601String(),
        ];
    }
}
