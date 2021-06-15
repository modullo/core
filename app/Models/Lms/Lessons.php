<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lessons extends Model
{
    protected $guarded = [];

    protected $table = 'lms_lessons';

    public function assets(): BelongsTo
    {
        return $this->belongsTo(Assets::class,'resource_id');
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class,'resource_id');
    }

}
