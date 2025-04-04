<?php
return [
    'super_ids' => env('HD_SUPER_ADMIN_IDS'),
    'ldap' => [
        'users' => [
            'root_group' => env('HD_USERS_ROOT_GROUP'),
            'dn' => env('HD_USERS_DN'),
            'dn_group' => env('HD_USERS_DN_GROUP'),
        ],
        'admin_dn' => env('HD_ADMINISTRATORS_DN_GROUP'),
        'base_dn' => env('LDAP_BASE_DN')
    ],
    'surm' => [
        'endpoint' => env('SURM_ENDPOINT'),
        'token' => env('SURM_BEARER_TOKEN')
    ]
];
