<?php

namespace App\Observers;


use App\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Models\Permission;

class UuidObserver
{
    /**
     * @param Model $model
     */
    public function creating(Model $model)
    {
        $model->uuid = Str::uuid()->toString();
    }
}