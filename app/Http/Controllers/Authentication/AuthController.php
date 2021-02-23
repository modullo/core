<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Classes\AuthClass;
use Illuminate\Http\Request;
class AuthController extends Controller
{

  /**
   * @var AuthClass
   */
  private AuthClass $authClass;

  public function __construct(){
    $this->authClass  = new AuthClass;
  }
  public function register(Request $request){
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

}