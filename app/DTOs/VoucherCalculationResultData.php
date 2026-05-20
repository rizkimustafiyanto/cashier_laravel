<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class VoucherCalculationResultData
{
    public function __construct(
        public float $basePrice,
        public float $discountAmount,
        public float $finalPrice,
    ) {}
}

?>