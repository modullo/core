<?php

namespace App\Http\Controllers\Lms\Learners;

use App\Classes\LMS\CourseClass;
use App\Classes\LMS\LearnerClass;
use App\Exceptions\ResourceNotFoundException;
use App\Models\Lms\Tenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CoursesController extends Controller
{
    private CourseClass $courseClass;
    private LearnerClass $learnerClass;
    private Tenants $tenants;
    public function __construct()
    {
        $this->courseClass = new CourseClass;
        $this->tenants = new Tenants;
        $this->learnerClass = new LearnerClass;
    }

    public function index(Request $request,string $programId){
        $user = $request->user();
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->learner->tenant_id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->courseClass->fetchAllCourses($search, $tenant->id,$programId,'all',$limit);
    }
    public function all(Request $request){
        $user = $request->user();
        $search = $request->query('search') ?? '';
        $limit = $request->query('limit', 100);
        $tenant = $this->tenants->newQuery()->where('lms_user_id',$user->learner->tenant_id)->first();
        if (!$tenant) throw new ResourceNotFoundException('tenant could not be found');
        return $this->courseClass->fetchAllCourses($search, $tenant->id,null,'all',$limit);
    }


}
