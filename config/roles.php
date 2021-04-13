<?php
return [
  "roles" => [
    ['name' => 'overlord','guard_name' => 'api','description' => 'Overlord with every permissions','display_name' => 'Overlord',"level" => "overlord"],
    ['name' => 'administrative','guard_name' => 'api','description' => 'Admin  with couple of modullo permissions','display_name' => 'Admin',"level" => "administrative"],
    ['name' => 'tenant','guard_name' => 'api','description' => 'Modullo System Tenant','display_name' => 'Tenant ',"level" => "tenant"],
    ['name' => 'user','guard_name' => 'api','description' => 'Modullo System User','display_name' => 'user ',"level" => "user"],
  ]
];