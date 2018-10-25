<?php

return [
    'resolver' => '',
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
    'import_limit' => 9999
];
