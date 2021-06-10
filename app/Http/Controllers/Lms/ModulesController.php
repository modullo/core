<?php

namespace App\Http\Controllers\Lms;

use App\Classes\Lms\ModulesClass;
use App\Http\Controllers\Controller;
use App\Models\Lms\Modules;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ModulesController extends Controller
{
    private ModulesClass $modulesClass;

    public function __construct(){
        $this->modulesClass = new ModulesClass;
    }

    /**
     * @throws ValidationException
     */
    public function create(Request $request, string $courseId){
        $this->validate($request,[
            "title" => "required",
            "description" => "required",
            "duration" => "required",
            "module_number" => "required|numeric"
        ]);
        $check = Module::where(['course_id' => $courseId,'module_number'=>$request->module_number])->exists();
        if($check){
            throw new CustomValidationFailed('the module number has already been taken for the course');
        }
    }


}
