<?php
return [
  "permissions" => [
    [
      'permission' => 'overlord_rights',
      'display_name' => 'Overlord Permission',
      'description' => 'This Permission is for the overlord user of modullo',
      'guard_name' => 'api',
      'level' => 'overlord'
    ],
    [
      'permission' => 'manage_admins',
      'display_name' => 'Admin Management Permission',
      'description' => 'The Overlord User Can Manage Admins',
      'guard_name' => 'api',
      'level' => 'overlord'
    ],

    ['permission' => 'manage_roles',
     'display_name'=> 'Manage Roles and  Permissions',
     'description' => 'This user can manage roles and permissions',
     "level" => "administrative",
     'guard_name' => 'api'
    ]
  ]
];