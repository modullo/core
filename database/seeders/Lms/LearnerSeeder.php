<?php


namespace Database\Seeders\Lms;


use App\Exceptions\ResourceNotFoundException;
use App\Models\Lms\Learners;
use App\Models\Lms\Tenants;
use App\Models\Lms\Courses;
use App\Models\Lms\LearnerCourses;
use App\Models\Lms\User;
use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LearnerSeeder extends Seeder
{

    public function run()
    {
        $user = User::updateOrCreate(["email" => 'newlearner@gmail.com'],
            [
                "email" => 'newlearner@gmail.com',
                "password" => Hash::make('ZSf_q36PedG78F8B'),
            ]
        );
        $role = Role::where('name','lms_learner')->first();
        if($role === null){
            throw new ResourceNotFoundException('Role could not be found');
        }
        $user->assignRole($role);
        $tenant = Tenants::where('company_name', 'like','%transformation teachers network%')->first();
        $learner = Learners::updateOrCreate(['lms_user_id' => $user->id],[
            'tenant_id' => $tenant->id,
            'first_name' => 'New',
            'last_name' => 'Learner',
            'phone_number' => '00000000',
            'gender' => 'female',
        ]);
        $courses = Courses::where('tenant_id',$tenant->id)->get();
        foreach($courses as $course){
            if($course->tenant_id === $learner->tenant_id){
            $this->subscribeToCourse($learner->id,$course->id,$learner->tenant_id,$course->program_id);
            }
        }

    }

    protected function subscribeToCourse(string $learnerId, string $courseId, string $tenantId,
        string $programId ){
        LearnerCourses::updateOrCreate([
            'tenant_id' => $tenantId,
            'course_id' => $courseId,
            'learner_id' => $learnerId,
        ],
        [
            'tenant_id' => $tenantId,
            'course_id' => $courseId,
            'learner_id' => $learnerId,
             'program_id' => $programId,
            'started_date' => Carbon::now(),
        ]);
    }

}