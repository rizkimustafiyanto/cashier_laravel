<?php

declare(strict_types=1);

namespace App\Services\PDF;

use App\Models\Transactions;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoicePdfService
{
  /**
   * Generate a PDF invoice for a given transaction.
   */

  public function download(Transactions $transaction,): StreamedResponse
  {
    $pdf = Pdf::loadView('pdf.invoice', [
        'transaction' => $transaction,
    ]);

    return response()->streamDownload(
        fn () => print($pdf->output()),
        $transaction->invoice_number.'.pdf',
    );
  }
}