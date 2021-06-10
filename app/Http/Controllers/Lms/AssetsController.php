<?php

namespace App\Http\Controllers\Lms;

use App\Classes\Lms\AssetsClass;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    protected AssetsClass $assetsClass;

    public function __construct(){
        $this->assetsClass = new AssetsClass;
    }
}
