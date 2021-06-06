<?php

namespace App\Models\Lms;

use App\Traits\UuidGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programs extends Model
{
    use SoftDeletes;

    protected $table = 'lms_programs';

    protected $guarded = [];

    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tenants::class);
    }

}
