<?php

namespace App\Http\Resources\Lms;

use App\Http\Resources\Lms\UserResource as UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantsResource extends JsonResource
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
            'user' => new UserResource($this->lmsUser),
            'company'=> $this->company_name,
            'country' => $this->country,
            'created_at' => (string) $this->created_at->toIso8601String(),
            'updated_at' => (string) $this->updated_at->toIso8601String(),
        ];
    }
}
