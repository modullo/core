<?php

namespace App\Models;

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
    use Authenticatable,  Authorizable, HasFactory, SoftDeletes, HasRoles , HasApiTokens, UuidGenerator;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
  protected $hidden = ['password', 'remember_token'];

  /**
   * Adds a 'name' attribute on the model
   *
   * @return string
   */
  public function getNameAttribute(): string
  {
    return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
  }


}
