<?php


namespace Database\Seeders\Lms;


use App\Models\Lms\Tenants;
use App\Models\Lms\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{

    public function run()
    {
        $user = User::updateOrCreate(["email" => 'gbengaspeaks@gmail.com'],
        [
            "email" => 'gbengaspeaks@gmail.com',
            "password" => Hash::make('ZSf_q36PedG78E8B'),
        ]
        );
        $role = Role::where('name','lms_tenant')->first();
        if($role === null){
            throw new NotFoundResourceException('Role could not be found');
        }
        $user->assignRole($role);

        $tenant = Tenants::updateOrCreate(['lms_user_id' => $user->id],[
            'company_name' => 'transformation teachers network',
        ]);

    }

}