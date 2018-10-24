<?php

return [
    'auditable' => [
        'bucket' => env('AWS_BUCKET_AUDITABLE'),
        'include' => [
            'tables' => '.*',
            'tenants' => '.*'
        ],
        'exclude' => [
            'tables' => '(log.*|cache)',
            'tenants' => '(demo|test)'
        ],
    ],
    'api_key' => env('API_KEY')
];
