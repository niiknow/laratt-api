<?php

return [
    'auditable' => [
        'bucket' => env('AWS_BUCKET_AUDITABLE'),
        'include' => [
            'table' => '.*',
            'tenant' => '.*'
        ],
        'exclude' => [
            'table' => '(log.*|cache)',
            'tenant' => '(demo|test)'
        ],
    ],
    'api_key' => env('API_KEY'),
    'import_limit' => 9999
];
