<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Services\Report\DashboardService;

class GetDashboardSummaryAction
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function execute(): array
    {
        return [
          'summary' => $this->dashboardService->getSummary(),

          'recent_transactions' => $this->dashboardService->getRecentTransactions(),

          'top_procedures' => $this->dashboardService->getTopProcedures(),

          'voucher_usage' => $this->dashboardService->getVoucherUsageSummary(),

                    'top_insurances_visits' => $this->dashboardService->getTopInsurancesByVisits(),

                    'top_insurances_revenue' => $this->dashboardService->getTopInsurancesByRevenue(),

        ];
    }
}