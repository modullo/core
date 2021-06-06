<?php

namespace App\Http\Controllers\Lms;

use App\Classes\Lms\ProgramClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ProgramsController extends Controller
{
    private ProgramClass $programClass;
    public function __construct(){
        $this->programClass = new ProgramClass;
    }


    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create(Request $request){
        $user = $request->user();
        $this->validate($request, [
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|string',
            'video_overview' => 'nullable|string',
            'type' => 'required|in:paid,free',
        ]);

        return  $this->programClass->createProgram($request->all(), $user);
    }
}
