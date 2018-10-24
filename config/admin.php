<?php

return [
    'auditable' => [
        'bucket' => env('AWS_BUCKET_AUDITABLE'),
        'include' => [
            'tables' => '.*',  // include all
            'tenants' => '.*'  // include all
        ],
        'exclude' => [
            'tables' => '(log.*|cache)', // exclude log2015 or cache
            'tenants' => '(demo|test)'   // exclude demo or test tenant
        ],
    ],
    'token' => env('ADMIN_TOKEN')
];
