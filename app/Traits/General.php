<?php


namespace App\Traits;


use App\Models\Lms\Tenants;
use App\Models\Lms\User;
use Http\Discovery\Exception\NotFoundException;
use Illuminate\Database\Eloquent\Model;

trait General
{
    public function findUser(string $uuid){
        $user = new User;
        $foundUser = $user->newQuery()->where('uuid',$uuid)->first();
        if (!$foundUser) throw new NotFoundException('unfortunately the user could not be found in the system');
        return $foundUser;
    }

}