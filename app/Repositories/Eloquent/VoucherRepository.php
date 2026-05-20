<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Voucher;
use App\Repositories\Contracts\VoucherRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VoucherRepository implements VoucherRepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Voucher::query()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getActive(): Collection
    {
        return Voucher::query()
            ->where('is_active', true)
            ->where('start_date', '<=', Carbon::now()->startOfDay())
            ->where('end_date', '>=', Carbon::now()->endOfDay())
            ->get();
    }

    public function find(string $id): ?Voucher
    {
        return Voucher::find($id);
    }

    public function create(array $data): Voucher
    {
        return Voucher::create($data);
    }

    public function update(Voucher $voucher, array $data): Voucher
    {
        $voucher->update($data);

        return $voucher;
    }

    public function delete(Voucher $voucher): bool
    {
        return (bool) $voucher->delete();
    }

    public function findByInsuranceId(string $insuranceId): Collection
    {
        return Voucher::query()
            ->where('insurance_id', $insuranceId)
            ->get();
    }

    public function search(?string $query = null, ?bool $isActive = null): Collection
    {
        return Voucher::query()
            ->when($query, function (Builder $builder) use ($query) {
                $builder->where('insurance_name', 'like', "%{$query}%")
                    ->orWhere('insurance_id', 'like', "%{$query}%");
            })
            ->when($isActive !== null, function (Builder $builder) use ($isActive) {
                $builder->where('is_active', $isActive);
            })
            ->get();
    }
}
