<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            "program" => new ProgramsResource($this->whenLoaded('program')),
            "description" => $this->description,
            "course_image" => $this->course_image,
            "duration" => $this->duration,
            "skills_to_be_gained" => $this->skills_to_be_gained,
            "course_state" => $this->course_state,
            "html_formatted_description" => $this->html_formatted_description ?? '',
            "course_level" => $this->course_level,
            'created_at' => (string) $this->created_at->toIso8601String(),
            'updated_at' => (string) $this->updated_at->toIso8601String(),
        ];
    }
}
