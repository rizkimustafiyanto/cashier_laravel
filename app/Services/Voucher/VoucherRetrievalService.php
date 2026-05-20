<?php

declare(strict_types=1);

namespace App\Services\Voucher;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VoucherRetrievalService
{
    /**
     * Get all active vouchers for a specific insurance
     */
    public function getActiveVouchersForInsurance(string $insuranceId): Collection
    {
        return Voucher::query()
            ->where('insurance_id', $insuranceId)
            ->where('is_active', true)
            ->where('start_date', '<=', Carbon::now()->startOfDay())
            ->where('end_date', '>=', Carbon::now()->endOfDay())
            ->get();
    }

    /**
     * Check if voucher is applicable (valid period and active)
     */
    public function isVoucherApplicable(Voucher $voucher): bool
    {
        if (!$voucher->is_active) {
            return false;
        }

        $now = Carbon::now();

        return $now->isBetween(
            $voucher->start_date->startOfDay(),
            $voucher->end_date->endOfDay()
        );
    }

    /**
     * Get applicable voucher for insurance
     */
    public function getApplicableVoucher(string $insuranceId): ?Voucher
    {
        return Voucher::query()
            ->where('insurance_id', $insuranceId)
            ->where('is_active', true)
            ->where('start_date', '<=', Carbon::now()->startOfDay())
            ->where('end_date', '>=', Carbon::now()->endOfDay())
            ->first();
    }

    /**
     * Search vouchers with filters
     */
    public function searchVouchers(
        ?string $search = null,
        ?bool $isActive = null,
        ?string $type = null
    ): Collection {
        $query = Voucher::query();

        if ($search) {
            $query->where('insurance_name', 'like', "%{$search}%")
                ->orWhere('insurance_id', 'like', "%{$search}%");
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        if ($type) {
            $query->where('type', $type);
        }

        return $query->get();
    }

    /**
     * Get expiring vouchers (within 7 days)
     */
    public function getExpiringVouchers(): Collection
    {
        $today = Carbon::now()->startOfDay();
        $week = $today->copy()->addDays(7);

        return Voucher::query()
            ->where('is_active', true)
            ->whereBetween('end_date', [$today, $week])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Get expired vouchers
     */
    public function getExpiredVouchers(): Collection
    {
        return Voucher::query()
            ->where('end_date', '<', Carbon::now()->startOfDay())
            ->where('is_active', true)
            ->get();
    }
}
