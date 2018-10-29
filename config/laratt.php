<?php

return [
    'resolver' => '',
    'audit' => [
        'disk'    => env('AUDIT_DISK', 's3'),
        'bucket'  => env('AUDIT_BUCKET'),
        'include' => [
            'table'  => '.*',
            'tenant' => '.*'
        ],
        'exclude' => [
            'table'  => '(log.*|cache)',
            'tenant' => null
        ],
    ],
    'import_limit' => 9999
];
