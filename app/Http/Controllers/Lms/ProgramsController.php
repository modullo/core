<?php

namespace App\Http\Controllers\Lms;

use App\Classes\Lms\ProgramClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ProgramsController extends Controller
{
    private ProgramClass $programClass;
    public function __construct(){
        $this->programClass = new ProgramClass;
    }


    public function index(Request $request){
        $user = $request->user();
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        return $this->programClass->fetchAllPrograms($search, $limit, $user);
    }
    /**
     * @throws ValidationException
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

    public function single(Request $request, string  $programId)
    {
        return $this->programClass->getSingleProgram($programId);
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, string  $programId)
    {
        $this->validate($request, [
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'video_overview' => 'nullable|string',
            'type' => 'nullable|in:paid,free',

        ]);
        $programData = $request->only(['title', 'description', 'image', 'video_overview', 'type', 'price']);
        return $this->programClass->updateProgram($programData, $programId);
    }
}
