<?php

namespace App\Classes\Lms;

use App\Http\Resources\Lms\UserResource;
use App\Models\Lms\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AuthClass
{


    /**
     * @var User
     */
    private User $user;

    public function __construct()
    {
        $this->user = new User;

    }

    public function register(
        string $email,
        string $password
    ): Response
    {
        $newUser = null;
        DB::transaction(function () use (&$user, $email, $password) {
            $user = $this->user->create([
                'email' => $email,
                'password' => Hash::make($password),
            ]);
            //send welcome email here
        });
        $resource = new UserResource($user);
        return response()->created('user successfully created', $resource, 'user');
    }


    public function showUserDetails(string $id): Response
    {
        try {
            $user = $this->user->newQuery()->findOrFail($id);
            return response()->fetch('user successfully fetched', new UserResource($user), 'user');
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

    }

}