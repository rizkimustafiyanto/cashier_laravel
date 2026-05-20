<?php

declare(strict_types=1);

namespace App\Actions\Transaction;

use App\Models\Transactions;
use App\Services\PDF\InvoicePdfService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadInvoiceAction
{
    public function __construct(
        private readonly InvoicePdfService $service,
    ) {}

    /**
     * Execute invoice download.
     */
    public function execute(
        Transactions $transaction,
    ): StreamedResponse {

        if (is_null($transaction->paid_at)) {
            abort(403, 'Invoice can only be downloaded for paid transactions.');
        }

        return $this->service
            ->download(
                $transaction->load([
                    'items',
                    'cashier',
                ])
            );
    }
}