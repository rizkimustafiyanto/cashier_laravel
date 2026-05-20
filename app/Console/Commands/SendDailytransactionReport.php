<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\DailyTransactionReportMail;
use App\Services\Report\DailyTransactionReportService;
use App\Services\Telegram\TelegramNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDailyTransactionReport extends Command
{
    /**
     * Command signature.
     */
    protected $signature =
        'report:daily-transactions';

    /**
     * Command description.
     */
    protected $description =
        'Send daily transaction report';

    /**
     * Execute command.
     */
    public function handle(
        DailyTransactionReportService $reportService,
        TelegramNotificationService $telegramService,
    ): int {

        try {

            $date = Carbon::yesterday();

            $filePath = $reportService
                ->generateReport($date);

            Mail::to(config('report.email_recipient'))
                ->send(new DailyTransactionReportMail($filePath, $date));

            $telegramService->sendDocument(
                message: sprintf(
                    'Daily transaction report %s',
                    $date->format('Y-m-d'),
                ),
                filePath: $filePath,
            );

            $this->info('Daily report sent successfully.');

            return self::SUCCESS;

        } catch (Throwable $exception) {

            report($exception);

            $this->error(
                $exception->getMessage(),
            );

            return self::FAILURE;
        }
    }
}