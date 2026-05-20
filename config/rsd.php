<?php

declare(strict_types=1);

return [
    'base_url' => env('RSD_API_URL'),
    'email' => env('RSD_API_EMAIL'),
    'password' => env('RSD_API_PASSWORD'),
    'timeout' => env('RSD_API_TIMEOUT', 10),
];
