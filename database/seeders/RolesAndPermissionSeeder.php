<?php


namespace Database\Seeders;


use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionSeeder extends Seeder
{

  public function run()
  {
    // Reset cached roles and permissions
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


    $permissions = config('system_permissions.permissions');
    $systemSetup = config('setup');
    $roles = config('roles.roles');

    foreach ($roles as $role) {

      $role = Role::updateOrCreate(['name' => $role['name'],'guard_name' => $role['guard_name'],'description' =>
        $role['description'],'display_name' => $role['display_name'],"level" => $role['level']]);
    }

    $admin = \App\Models\User::where('email',$systemSetup['settings']['overlord_email'])->first();
    $overlord = Role::whereName('overlord')->first();
    $admin->assignRole($overlord);

    foreach ($permissions as $permission) {
      $permission = Permission::updateOrCreate(['name' => $permission['permission']],['name' => $permission['permission'],'description' => $permission['description'],'guard_name' => 'api','level' => $permission['level'],'display_name' => $permission['display_name'] ]);
      $admin->givePermissionTo($permission);
      app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    $tenant_permissions = config('tenant_permissions.permissions');

    foreach ($tenant_permissions as $permission) {
      $permission = Permission::updateOrCreate(['name' => $permission['permission']],['name' => $permission['permission'],'description' => $permission['description'],'guard_name' => 'api','level' => $permission['level'],'display_name' => $permission['display_name'] ]);
      app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }


    $user_permissions = config('user_permissions.permissions');

    foreach ($user_permissions as $permission) {
      $permission = Permission::updateOrCreate(['name' => $permission['permission']],['name' => $permission['permission'],'description' => $permission['description'],'guard_name' => 'api','level' => $permission['level'],'display_name' => $permission['display_name'] ]);
      app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

  }

}