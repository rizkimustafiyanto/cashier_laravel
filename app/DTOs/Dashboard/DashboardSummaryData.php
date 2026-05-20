<?php

declare(strict_types=1);

namespace App\DTOs\Dashboard;

readonly class DashboardSummaryData
{
    public function __construct(
        public int $totalTransactions,
        public float $totalRevenue,
        public float $totalDiscount,
        public int $totalProcedures,
    ) {
    }
}