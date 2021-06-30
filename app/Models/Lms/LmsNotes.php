<?php

namespace App\Models\Lms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LmsNotes extends Model {
	use SoftDeletes;
	protected $guarded = [];

	public function modules(): \Illuminate\Database\Eloquent\Relations\BelongsTo {
		return $this->belongsTo(Modules::class);
	}
}
