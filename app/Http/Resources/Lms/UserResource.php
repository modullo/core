<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'=> $this->uuid,
            'email'=>$this->email,
            'user_type'=>$this->user_type,
            "role_display_name" =>ucwords(join(" ",explode("_",$this->getRoleNames()[0]))) ?? "Not Available",
            "role" => $this->getRoleNames()[0] ?? null,
        ];
    }
}
