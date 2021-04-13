<?php
namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
  protected $primaryKey = 'id';
  public $incrementing = false;

  /**
   * The attributes that should be cast to native types.
   *
   * @var array
   */
  protected $casts = [
    'id' => 'string'
  ];
}