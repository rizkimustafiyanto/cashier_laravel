<?php

declare (strict_types=1);

namespace App\DTOs;

readonly class CreateTransactionData
{
    /**
     * @param TransactionItemData[] $items
     */
    public function __construct(
        public string $patientName,
        public ?string $patientEmail,
        public ?string $patientPhone,
        public ?string $patientGender,
        public ?string $patientDob,
        public ?string $insuranceId,
        public array $items,
        public string $cashierId,
    ) {}
}