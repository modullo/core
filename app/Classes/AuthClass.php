<?php

namespace App\Classes;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthClass
{


  /**
   * @var User
   */
  private $user;

  public function __construct(){
    $this->user = new User;

  }

  public function register(
    string $email,
    string $first_name,
    string $last_name,
    string $phone_number,
    string $password
  ){
    if (strlen($last_name) > 30) {
      substr($last_name, 0, 30);
    }

    $newUser = null;
    DB::transaction(function () use (&$user,$last_name,$email,$first_name,$phone_number,$password) {
        $user = $this->user->create([
          'first_name' => $first_name,
          'last_name' => $last_name,
          'email' => $email,
          'password' => Hash::make($password),
          'phone_number' => $phone_number,
        ]);

      //send welcome email here

    });
    $resource = new UserResource($user);
    return response()->created('user successfully created',$resource, 'user');

  }

}