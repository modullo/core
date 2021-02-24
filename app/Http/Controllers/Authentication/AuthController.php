<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Classes\AuthClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{

  /**
   * @var AuthClass
   */
  private AuthClass $authClass;

  public function __construct(){
    $this->authClass  = new AuthClass;
  }


  public function register(Request $request): Response
  {
    $this->validate($request,[
      'email' => 'required|email|unique:users',
      'password' => 'required|min:8',
      'first_name' => 'required|max:30',
      'last_name' => 'required|max:80',
      'phone_number' => 'required|max:30'
    ]);

    $first_name = $request->input('first_name');
    $last_name = $request->input('last_name');
    $email = $request->input('email');
    $password = $request->input('password');
    $phone = $request->input('phone_number');
    return $this->authClass->register($email,$first_name,$last_name,$phone,$password);
  }


  public function getUser(Request  $request): Response
  {
    $userId = $request->user()->id;
    return $this->authClass->showUserDetails($userId);
  }

}