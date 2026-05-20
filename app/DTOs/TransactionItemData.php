<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class TransactionItemData
{
    public function __construct(
        public string $procedureId,
        public string $procedureName,
    ) {}
}
