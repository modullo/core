<?php

namespace App\Models\Lms;

use App\Http\Resources\Lms\CourseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $table = 'lms_modules';

    public function course(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Courses::class);
    }

    public function lessons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lessons::class,'module_id')->orderBy('lesson_number');
    }
}
