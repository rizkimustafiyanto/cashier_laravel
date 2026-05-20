<?php

declare(strict_types=1);

namespace App\Actions\Voucher;

use App\Models\Voucher;

class DeleteVoucherAction
{
    public function __invoke(Voucher $voucher): bool
    {
        return $voucher->delete();
    }
}
