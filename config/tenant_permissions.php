<?php
return [
  "permissions" => [
    [
      'permission' => 'tenant_rights',
      'display_name' => 'Tenant Default Permission',
      'description' => 'This Permission is for the tenant user of modullo',
      'guard_name' => 'api',
      'level' => 'tenant'
    ],

    [
      'permission' => 'manage_users',
      'display_name' => 'Tenant User Management',
      'description' => 'This Permission is for the tenant to manage users',
      'guard_name' => 'api',
      'level' => 'tenant'
    ],
  ]
];