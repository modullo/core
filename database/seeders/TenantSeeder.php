<?php


namespace Database\Seeders;


use App\Models\Lms\Tenants;
use App\Models\Lms\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{

    public function run()
    {
        $user = User::updateOrCreate(["email" => 'tomide@hostville.website'],
        [
            "email" => 'tomide@hostville.website',
            "password" => Hash::make('password'),
        ]
        );

        $tenant = Tenants::updateOrCreate(['lms_user_id' => $user->id],[
            'company_name' => 'hostville',
        ]);

    }

}