<?php

declare(strict_types=1);

namespace App\Enums;

enum VoucherTypes: string
{
    case Percentage = 'percentage';
    case FixedAmount = 'fixed';
    case None = 'none';
}
