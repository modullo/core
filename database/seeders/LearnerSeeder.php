<?php


namespace Database\Seeders;


use App\Exceptions\ResourceNotFoundException;
use App\Models\Lms\Learners;
use App\Models\Lms\Tenants;
use App\Models\Lms\User;
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

    }

}