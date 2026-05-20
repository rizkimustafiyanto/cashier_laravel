<?php

declare(strict_types=1);

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DailyTransactionReportMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly string $filePath,
        private readonly Carbon $date,
    ) {
    }

    public function build(): self
    {
        return $this->subject(sprintf('Daily transaction report %s', $this->date->format('Y-m-d')))
            ->view('emails.daily-transaction-report')
            ->with(['date' => $this->date])
            ->attach($this->filePath, [
                'as' => basename($this->filePath),
                'mime' => 'text/csv',
            ]);
    }
}
