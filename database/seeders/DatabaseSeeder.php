<?php

namespace Database\Seeders;

use Database\Seeders\Lms\LearnerSeeder;
use Database\Seeders\Lms\RolesAndPermissionSeeder;
use Database\Seeders\Lms\TenantSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->call([OverlordSeeder::class,RolesAndPermissionSeeder::class,TenantSeeder::class, LearnerSeeder::class]);
    }
}
