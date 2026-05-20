<?php

declare(strict_types=1);

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),
    'chat_id' => array_filter(array_map('trim', explode(',', env('TELEGRAM_CHAT_ID', '')))),
];
