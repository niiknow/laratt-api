<?php

return [
    'audit' => [
        'bucket' => env('AWS_BUCKET_AUDIT'),
        'include' => [
            'table' => '.*',
            'tenant' => '.*'
        ],
        'exclude' => [
            'table' => '(log.*|cache)',
            'tenant' => null
        ],
    ],
    'api_key' => env('API_KEY'),
    'import_limit' => 9999
];
