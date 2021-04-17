<?php


namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OverlordSeeder extends Seeder
{

  public function run()
  {
    //
    $systemSetup = config('setup.settings');
    User::updateOrCreate(["email" => $systemSetup['overlord_email']],[
      "email" => $systemSetup['overlord_email'],
      "password" => Hash::make($systemSetup['overlord_password']),
      "first_name" => "Overlord",
      "last_name" => "Admin",
      "phone_number" => "08177171797",
      "gender" => 'male',
      "is_verified" => true
    ]);

  }

}