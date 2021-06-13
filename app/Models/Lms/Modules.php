<?php

namespace App\Models\Lms;

use App\Http\Resources\Lms\CourseResource;
use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    protected $guarded = [];

    protected $table = 'lms_modules';

    public function course(){
        return $this->belongsTo(CourseResource::class);
    }
}
