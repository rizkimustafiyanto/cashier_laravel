<?php

declare(strict_types=1);

namespace App\Actions\Voucher;

use App\Models\Voucher;
use Illuminate\Support\Carbon;

class UpdateVoucherAction
{
    public function __invoke(Voucher $voucher, array $data): Voucher
    {
        $voucher->update([
            'insurance_id' => $data['insuranceId'],
            'insurance_name' => $data['insuranceName'],
            'type' => $data['type'],
            'value' => (float) $data['value'],
            'max_discount' => isset($data['maxDiscount']) ? (float) $data['maxDiscount'] : null,
            'start_date' => Carbon::parse($data['startDate'])->startOfDay(),
            'end_date' => Carbon::parse($data['endDate'])->endOfDay(),
            'is_active' => $data['isActive'] ?? true,
            'updated_by' => $data['updated_by'],
        ]);

        return $voucher;
    }
}
