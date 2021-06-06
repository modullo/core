<?php

namespace App\Models\Lms;

use App\Traits\UuidGenerator;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Tenants extends Model
{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes, HasRoles, HasApiTokens;

    protected $table = 'lms_tenants';
    protected $guarded = [];

    protected string $guard = 'tenant';

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function lmsUser()
    {
        return $this->belongsTo(User::class);
    }
}
