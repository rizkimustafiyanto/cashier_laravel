<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\DTOs\Dashboard\DashboardSummaryData;
use App\Models\Transactions;
use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
  /**
   * Get Summary
   */
  public function getSummary(): DashboardSummaryData
  {
    $data = Cache::remember('dashboard_summary', now()->addMinutes(5), function () {
      $summary = Transactions::query()
        ->selectRaw('
          COUNT(*) AS total_transactions,
          COALESCE(SUM(grand_total), 0) AS total_revenue,
          COALESCE(SUM(discount_total), 0) AS total_discount
        ')->first();

      $totalProcedures = TransactionItem::query()
        ->count();
      
      return [
        'totalTransactions' => (int) $summary->total_transactions,
        'totalRevenue' => (float) $summary->total_revenue,
        'totalDiscount' => (float) $summary->total_discount,
        'totalProcedures' => (int) $totalProcedures,
      ];
    });

    return new DashboardSummaryData(
      totalTransactions: $data['totalTransactions'],
      totalRevenue: $data['totalRevenue'],
      totalDiscount: $data['totalDiscount'],
      totalProcedures: $data['totalProcedures'],
    );
  }

  /**
   * Recent Transactions
   */

  public function getRecentTransactions(int $limit = 10): array
  {
    return Transactions::query()
      ->with(['cashier','items'])
      ->latest()
      ->limit($limit)
      ->get()
      ->all();
  }

  /**
   * Top Procedures
   */

  public function getTopProcedures(): Collection
  {
    return TransactionItem::query()
      ->select('procedure_name')
      ->selectRaw('
        COUNT(*) AS total_used,
        COALESCE(SUM(final_price), 0) AS total_revenue
      ')
      ->groupBy('procedure_name')
      ->orderByDesc('total_used')
      ->limit(5)
      ->get();
  }

  /**
   * Voucher usage summary
   */

  public function getVoucherUsageSummary(): Collection
  {
    return TransactionItem::query()
      ->select('voucher_type')
      ->selectRaw('
        COUNT(*) AS total_usage,
        COALESCE(SUM(discount_amount), 0) AS total_discount
      ')
      ->whereNotNull('voucher_type')
      ->groupBy('voucher_type')
      ->get();
  }

      /**
       * Top Insurances by Visits
       */

      public function getTopInsurancesByVisits(int $limit = 5): Collection
      {
        return Transactions::query()
          ->select('insurance_id', 'insurance_name')
          ->selectRaw('COUNT(*) AS total_visits, COALESCE(SUM(grand_total), 0) AS total_revenue')
          ->whereNotNull('insurance_id')
          ->groupBy('insurance_id', 'insurance_name')
          ->orderByDesc('total_visits')
          ->limit($limit)
          ->get();
      }

      /**
       * Top Insurances by Revenue
       */

      public function getTopInsurancesByRevenue(int $limit = 5): Collection
      {
        return Transactions::query()
          ->select('insurance_id', 'insurance_name')
          ->selectRaw('COALESCE(SUM(grand_total), 0) AS total_revenue, COUNT(*) AS total_visits')
          ->whereNotNull('insurance_id')
          ->groupBy('insurance_id', 'insurance_name')
          ->orderByDesc('total_revenue')
          ->limit($limit)
          ->get();
      }
}
