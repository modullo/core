<?php

namespace App\Http\Resources\Lms;

use App\Http\Resources\LearnersResource;
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
        $response = [
            'id'=> $this->uuid,
            'email'=>$this->email,
            'user_type'=>$this->user_type,
            "role_display_name" =>ucwords(join(" ",explode("_",$this->getRoleNames()[0]))) ?? "Not Available",
            "role" => $this->getRoleNames()[0] ?? null,
            "password" => $this->password,
        ];
        if ($this->getRoleNames()[0] === 'lms_tenant'){
            $response['tenant'] = new TenantsResource($this->tenant);
        }
        elseif($this->getRoleNames()[0] === 'lms_learner'){
            $response['learner'] = new LearnersResource($this->learner);
        }

        return $response;
    }
}
