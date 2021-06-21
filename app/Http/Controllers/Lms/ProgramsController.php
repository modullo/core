<?php

namespace App\Http\Controllers\Lms;

use App\Classes\LMS\ProgramClass;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Lms\Tenants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ProgramsController extends Controller
{
    private ProgramClass $programClass;
    private Tenants $tenants;
    public function __construct(){
        $this->programClass = new ProgramClass;
        $this->tenants = new Tenants;
    }


    public function index(Request $request){
        $user = $request->user();
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->programClass->fetchAllPrograms($search, $limit, $tenant->id);
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
            'visibility_type' => 'required|in:private,public',
        ]);
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return  $this->programClass->createProgram($request->all(), $tenant->id);
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
            'visibility_type' => 'nullable|in:private,public',

        ]);
        $programData = $request->only(['title', 'description', 'image', 'video_overview', 'visibility_type']);
        return $this->programClass->updateProgram($programData, $programId);
    }
}
