<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class UserResource extends JsonResource
{
  /**
   * Transform the resource collection into an array.
   *
   * @param  Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'id'=> $this->uuid,
      'first_name'=> $this->first_name,
      'last_name' => $this->last_name,
      'name' => $this->name,
      'email'=>$this->email,
      'password'=>$this->password,
      'phone_number'=>$this->phone_number,
      'gender'=>$this->gender,
      'verified'=>(boolean) $this->verified
      ];
  }

}