<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizQuestionsResource extends JsonResource
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
            "question_text" => $this->question_text,
            "score" =>$this->score,
            "answer" => $this->answer,
            'question_type' => $this->question_type,
            "options" => json_decode($this->options,true)

        ];
    }
}
