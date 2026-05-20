<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Voucher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface VoucherRepositoryInterface
{
    /**
     * Get all vouchers with pagination
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all active vouchers
     */
    public function getActive(): Collection;

    /**
     * Find voucher by ID
     */
    public function find(string $id): ?Voucher;

    /**
     * Create a new voucher
     */
    public function create(array $data): Voucher;

    /**
     * Update a voucher
     */
    public function update(Voucher $voucher, array $data): Voucher;

    /**
     * Delete a voucher
     */
    public function delete(Voucher $voucher): bool;

    /**
     * Find by insurance ID
     */
    public function findByInsuranceId(string $insuranceId): Collection;

    /**
     * Search vouchers
     */
    public function search(?string $query = null, ?bool $isActive = null): Collection;
}
