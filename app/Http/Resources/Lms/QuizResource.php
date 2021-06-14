<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->uuid,
            "title" => $this->title,
            "total_quiz_mark" => (int)$this->total_quiz_mark,
            "quiz_timer" => (int)$this->quiz_timer,
            "disable_on_submit" => (boolean)$this->disable_on_submit,
            "retake_on_request" => (boolean)$this->retake_on_request,
            "questions" => QuizQuestionsResource::collection($this->whenLoaded('questions'))
        ];
    }
}
