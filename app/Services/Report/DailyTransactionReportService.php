<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Models\Transactions;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DailyTransactionReportService
{
    /**
     * Generate daily transaction report and export to CSV
     */
    public function generateReport(Carbon $date): string
    {
        $filename = sprintf('reports/daily_transaction_report_%s.csv', $date->format('d-m-Y'));
        $disk = Storage::disk('public');

        $disk->makeDirectory('reports');

        $filePath = $disk->path($filename);
        $handle = fopen($filePath, 'wb');

        if ($handle === false) {
            throw new RuntimeException(sprintf('Unable to create report file: %s', $filePath));
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, $this->headings());

        foreach ($this->rows($date) as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        return $filePath;
    }

    private function headings(): array
    {
        return [
            'Invoice Number',
            'Patient Name',
            'Patient Gender',
            'Cashier',
            'Subtotal',
            'Discount Total',
            'Grand Total',
            'Paid At',
        ];
    }

    private function rows(Carbon $date): Collection
    {
        return Transactions::query()
            ->with('cashier')
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ])
            ->orderBy('paid_at')
            ->get()
            ->map(fn (Transactions $transaction): array => [
                $transaction->invoice_number,
                $transaction->patient_name,
                $transaction->patient_gender,
                $transaction->cashier?->name ?? '-',
                $transaction->subtotal,
                $transaction->discount_total,
                $transaction->grand_total,
                $transaction->paid_at?->format('d-m-Y H:i:s'),
            ]);
    }
}
