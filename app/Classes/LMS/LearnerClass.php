<?php


namespace App\Classes\Lms;


use App\Exceptions\ResourceNotFoundException;
use App\Http\Resources\Lms\LearnersResource;
use App\Http\Resources\Lms\TenantsResource;
use App\Http\Resources\Lms\UserResource;
use App\Models\Lms\Learners;
use App\Models\Lms\User;
use App\Models\Lms\Tenants;
use App\Models\Role;
use App\Traits\updateModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Classes\ModulloClass;


class LearnerClass extends ModulloClass
{
    protected array $updateFields = [
        'first_name' => 'first_name',
        'last_name' => 'last_name',
        'phone_number' => 'phone_number',
        'image' => 'image',
        'gender' => 'gender',
        'location' => 'location',
        'extra_config' => 'extra_config',
    ];
    private Learners $learners;
    private Tenants $tenants;

    protected User  $user;

    public function __construct(){
        $this->learners = new Learners;
        $this->tenants = new Tenants;
        $this->user = new User;
    }
    public function createLearner(array $data, string $tenantId):Response{
        $newLearner = null;
        DB::transaction(function () use (&$newLearner,&$data, $tenantId){
            $tenantUser = $this->tenants->newQuery()->where('lms_user_id',$tenantId)->first();
            if (!$tenantUser) throw new ResourceNotFoundException('could not find the provided tenant');
            $lmsUser = $this->user->newQuery()->create([
                'email' => $data['email'],
                'password' =>  Hash::make($data['password'])
            ]);
            $role = Role::where('name','lms_learner')->first();
            if($role === null){
                throw new NotFoundResourceException('Role could not be found');
            }
            $lmsUser->assignRole($role);
            $newLearner = $this->learners->newQuery()->create([
                'lms_user_id' => $lmsUser->id,
                'tenant_id' => $tenantUser->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['first_name'],
                'image' => $data['image'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'gender' => $data['gender'] ?? null,
                'location' => $data['location'] ?? null,
            ]);
        });
        if (!$newLearner){
            throw new \LogicException('system is unable to create a learner');
        }
        $resource = new UserResource($lmsUser);
        return \response()->created('learner created successfully',$resource,'learner');
    }


    public function updateLearner(string $learnerId, array $data){
        $user = $this->findUser($learnerId);
        $learner = $this->learners->newQuery()->where('lms_user_id',$user->id)->first();
        if(!$learner) throw new NotFoundResourceException('Learner could not be found');
        $this->updateModelAttributes($learner, $data);
        $learner->save();
        $resource = new UserResource($user);
        return \response()->updated('learner updated successfully',$resource,'learner');
    }

    public function singleLearner(string $tenantId){
        $user = $this->findUser($tenantId);
        $builder = $this->learners->newQuery()->where('lms_user_id',$user->id);
        $learner = $builder->first();
        if (!$learner)  throw new NotFoundResourceException('could not find the given learner');

        $resource = new UserResource($user);
        return \response()->fetch('learner fetched successfully',$resource,'learner');
    }
}