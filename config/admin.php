<?php

return [
    'auditable' => [
        'bucket' => env('AWS_BUCKET_AUDITABLE'),
        'exclude' => [
            'tables' => ['log', 'cache'],
            'tenants' => ['demo', 'test']
        ]
    ],
    'token' => env('ADMIN_TOKEN')
];
