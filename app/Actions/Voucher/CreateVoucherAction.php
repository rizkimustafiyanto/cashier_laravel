<?php

declare(strict_types=1);

namespace App\Actions\Voucher;

use App\Models\Voucher;
use Illuminate\Support\Carbon;

class CreateVoucherAction
{
    public function __invoke(array $data): Voucher
    {
        return Voucher::create([
            'insurance_id' => $data['insuranceId'],
            'insurance_name' => $data['insuranceName'],
            'type' => $data['type'],
            'value' => (float) $data['value'],
            'max_discount' => isset($data['maxDiscount']) ? (float) $data['maxDiscount'] : null,
            'start_date' => Carbon::parse($data['startDate'])->startOfDay(),
            'end_date' => Carbon::parse($data['endDate'])->endOfDay(),
            'is_active' => $data['isActive'] ?? true,
            'created_by' => $data['created_by'],
        ]);
    }
}
