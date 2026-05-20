<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionStatus: string
{
    case Draft = 'draft';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
}