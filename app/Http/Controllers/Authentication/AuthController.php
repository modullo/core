<?php
namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Classes\AuthClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Laravel\Passport\ClientRepository;

class AuthController extends Controller
{

  /**
   * @var AuthClass
   */
  private AuthClass $authClass;

  public function __construct(){
    $this->authClass  = new AuthClass;
  }


  public function setup(){
      try {
        $name = config('app.name').' Personal Access Client';
        $redirect = env('APP_URL') ?? 'http://localhost';
        $client =   (new ClientRepository())->createPasswordGrantClient(null,$name,$redirect);
        return response()->created( 'requirements fully setup',['client_id' => $client->id,'client_secret'
        => $client->secret],'client');

      }
      catch(\Exception $e){
        throw new Exception($e->getMessage());
      }

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