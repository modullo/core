<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramsResource extends JsonResource
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
            'description' => $this->description,
            'image' => $this->image,
            'video_overview' => $this->video_overview,
            'type' => $this->type,
            'price' => $this->price,
            'created_at' => (string) $this->created_at->toIso8601String(),
            'updated_at' => (string) $this->updated_at->toIso8601String(),
        ];
    }
}
