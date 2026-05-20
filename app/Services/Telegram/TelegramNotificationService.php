<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class TelegramNotificationService
{
    public function __construct(private readonly Factory $http,)
    {}

    /**
     * Send telegram document
     */

    public function sendDocument(string $message, string $filePath): void
    {
        foreach (Arr::wrap(config('telegram.chat_id')) as $chatId) {
            $this->http
                ->attach('document', fopen($filePath, 'r'), basename($filePath))
                ->post(sprintf('https://api.telegram.org/bot%s/sendDocument', config('telegram.bot_token')), [
                    'chat_id' => $chatId,
                    'caption' => $message,
                ])
                ->throw();
        }
    }
}