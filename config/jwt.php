<?php

return [
    'private_key' => storage_path('jwt.key'),

    'public_key' => storage_path('jwt.key.pub'),

    'ttl' => env('JWT_TTL', 86400), // in seconds

    'leeway' => env('JWT_LEEWAY', 60), // in seconds

    'encrypt_algo' => 'RS256',

    'allowed_algo' => ['RS256'],
];
