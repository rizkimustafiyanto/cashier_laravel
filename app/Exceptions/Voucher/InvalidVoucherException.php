<?php

declare(strict_types=1);

namespace App\Exceptions\Voucher;

use Exception;

class InvalidVoucherException extends Exception
{
    public static function invalidType(string $type): self
    {
        return new self("Invalid voucher type: {$type}");
    }

    public static function invalidDateRange(): self
    {
        return new self('End date must be after start date');
    }

    public static function expiredVoucher(): self
    {
        return new self('Voucher has expired');
    }

    public static function inactiveVoucher(): self
    {
        return new self('Voucher is not active');
    }

    public static function notYetActive(): self
    {
        return new self('Voucher is not yet active');
    }

    public static function invalidValue(): self
    {
        return new self('Voucher value must be greater than 0');
    }
}
