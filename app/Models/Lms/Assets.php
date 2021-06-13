<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assets extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'lms_assets';
}
