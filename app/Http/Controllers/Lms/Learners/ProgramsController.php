<?php

namespace App\Http\Controllers\Lms\Learners;

use App\Classes\LMS\LearnerClass;
use App\Classes\LMS\ProgramClass;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Lms\Tenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ProgramsController extends Controller
{
    private ProgramClass $programClass;
    private LearnerClass $learnerClass;
    private Tenants $tenants;
    public function __construct(){
        $this->programClass = new ProgramClass;
        $this->learnerClass = new LearnerClass;
        $this->tenants = new Tenants;
    }


    public function index(Request $request){
        $user = $request->user();
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->learner->tenant_id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->programClass->fetchAllPrograms($search, $limit, $tenant->id);
    }
}
