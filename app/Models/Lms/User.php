<?php

namespace App\Models\Lms;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\UuidGenerator;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable,  Authorizable, HasFactory, SoftDeletes, HasRoles , HasApiTokens;


    protected $guarded = [];

    protected string $guard_name = 'lms_users';

    protected $table = 'lms_users';

    protected $hidden = ['password', 'remember_token'];

    public function tenant(){
        return $this->hasOne(Tenants::class, 'lms_user_id');
    }

    public function learner(){
        return $this->hasOne(Learners::class, 'lms_user_id');
    }




}
