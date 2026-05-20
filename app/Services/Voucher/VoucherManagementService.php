<?php

declare(strict_types=1);

namespace App\Services\Voucher;

use App\Models\Voucher;
use Carbon\Carbon;

class VoucherManagementService
{
    /**
     * Get voucher statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Voucher::count(),
            'active' => Voucher::where('is_active', true)->count(),
            'inactive' => Voucher::where('is_active', false)->count(),
            'expiring_soon' => Voucher::query()
                ->where('is_active', true)
                ->whereBetween('end_date', [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->addDays(7)->endOfDay(),
                ])
                ->count(),
            'expired' => Voucher::query()
                ->where('end_date', '<', Carbon::now()->startOfDay())
                ->count(),
        ];
    }

    /**
     * Auto-deactivate expired vouchers
     */
    public function deactivateExpiredVouchers(): int
    {
        return Voucher::query()
            ->where('is_active', true)
            ->where('end_date', '<', Carbon::now()->startOfDay())
            ->update([
                'is_active' => false,
                'updated_by' => null,
            ]);
    }

    /**
     * Get voucher usage analytics (for future implementation)
     */
    public function getVoucherUsageStats(Voucher $voucher): array
    {
        return [
            'voucher_id' => $voucher->id,
            'insurance_name' => $voucher->insurance_name,
            'type' => $voucher->type,
            'value' => $voucher->value,
            'is_active' => $voucher->is_active,
            'period' => [
                'start' => $voucher->start_date->format('Y-m-d'),
                'end' => $voucher->end_date->format('Y-m-d'),
            ],
        ];
    }
}
