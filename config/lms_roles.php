<?php

return [
    "roles" => [
        ['name' => 'lms_tenant','guard_name' => 'lms_users','description' => 'LMS System Tenant','display_name' => 'Modullo LMS Tenant ',"level" => "tenant"],
        ['name' => 'lms_learner','guard_name' => 'lms_users','description' => 'LMS System Learner','display_name' => 'Modullo LMS Learner ',"level" => "user"],
    ]
];