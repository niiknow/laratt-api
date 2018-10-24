<?php

return [
    'auditable' => [
        'bucket' => env('AWS_BUCKET_AUDITABLE')
    ],
    'token' => env('ADMIN_TOKEN')
];
