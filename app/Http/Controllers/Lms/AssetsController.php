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


    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function customUpload(Request $request)
    {
        $this->validate($request, [
            "asset_file" => "required|file",
        ]);
        return $this->assetsClass->storeToS3($request);

    }
}
