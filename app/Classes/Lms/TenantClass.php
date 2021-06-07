<?php


namespace App\Classes\Lms;


use App\Http\Resources\Lms\TenantsResource;
use App\Http\Resources\Lms\UserResource;
use App\Models\Lms\User;
use App\Models\Lms\Tenants;
use App\Models\Role;
use App\Traits\updateModel;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Classes\ModulloClass;


class TenantClass extends ModulloClass
{
    protected array $updateFields = [
        'country' => 'country',
        'company_name' => 'company_name',
    ];
    private Tenants $tenant;

    protected User  $user;

    public function __construct(){
        $this->tenant = new Tenants;
        $this->user = new User;
    }
    public function createTenant(array $data):Response{
        $newTenant = null;
        DB::transaction(function () use (&$newTenant,&$data){
            $lmsUser = $this->user->newQuery()->create([
               'email' => $data['email'],
               'password' =>  Hash::make($data['password'])
            ]);
            $role = Role::where('name','lms_tenant')->first();
            if($role === null){
                throw new NotFoundResourceException('Role could not be found');
            }
            $lmsUser->assignRole($role);
            $newTenant = $this->tenant->newQuery()->create([
                'lms_user_id' => $lmsUser->id,
                'company_name' => $data['company_name'],
            ]);
        });
        if (!$newTenant){
            throw new \LogicException('system is unable to create a tenant');
        }
        $newTenant = new TenantsResource($newTenant);
        return \response()->created('tenant created successfully',$newTenant,'tenant');
    }


    public function updateTenant(string $tenantId, array $data){
        $user = $this->findUser($tenantId);
        $tenant = $this->tenant->where('lms_user_id',$user->id)->first();
        if($tenant === null){
            throw new NotFoundResourceException('Tenant could not be found');
        }
        $this->updateModelAttributes($tenant, $data);
        $tenant->save();
        $tenant = new TenantsResource($tenant);
        return \response()->updated('tenant updated successfully',$tenant,'tenant');
    }

    public function singleTenant(string $tenantId){
        $user = $this->findUser($tenantId);
        $builder = $this->tenant->where('lms_user_id',$user->id);
        $tenant = $builder->first();
        if ($tenant === null){
            throw new NotFoundResourceException('could not find the given tenant');
        }
        $tenant = new TenantsResource($tenant);
        return \response()->fetch('tenant fetched successfully',$tenant,'tenant');
    }
}