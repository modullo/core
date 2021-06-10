<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    protected $table = 'lms_courses';

    protected $guarded = [];

    public function program(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Programs::class);
    }

    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tenants::class);
    }
}
