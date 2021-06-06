<?php

namespace App\Http\Controllers\Lms\Authentication;

use App\Classes\LMS\AuthClass;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\ClientRepository;

class AuthController extends Controller
{

    /**
     * @var AuthClass
     */
    private AuthClass $authClass;

    public function __construct()
    {
        $this->authClass = new AuthClass;
    }


    /**
     * @param string|null $provider
     * @return mixed
     * @throws Exception
     */
    public function setup(string $provider = 'lms_users')
    {
        try {
            $name = config('app.name') . ' Personal Access Client';
            $redirect = env('APP_URL') ?? 'http://localhost';
            $client = (new ClientRepository())->createPasswordGrantClient(null, $name, $redirect,$provider);
            return response()->created('requirements fully setup', ['client_id' => $client->id, 'client_secret'
            => $client->secret], 'client');

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }


    /**
     * @throws ValidationException
     */
    public function register(Request $request): Response
    {
        $this->validate($request, [
            'email' => 'required|email|unique:lms_users',
            'password' => 'required|min:8',
        ]);

        $email = $request->input('email');
        $password = $request->input('password');
        return $this->authClass->register($email, $password);
    }


    public function getUser(Request $request): Response
    {
        $userId = $request->user()->id;
        return $this->authClass->showUserDetails($userId);
    }

}