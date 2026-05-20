<?php

declare(strict_types=1);

use App\Models\Voucher;
use App\Services\Voucher\VoucherCalculationService;

it('calculates percentage voucher with max discount', function () {

    $voucher = Voucher::factory()->make([
        'type' => 'percentage',
        'value' => 10,
        'max_discount' => 20000,
    ]);

    $result = app(
        VoucherCalculationService::class
    )->calculate(
        500000,
        $voucher,
    );

    expect(
        $result->discountAmount
    )->toBe(20000.0);

    expect(
        $result->finalPrice
    )->toBe(480000.0);
});

it('calculates fixed voucher', function () {

    $voucher = Voucher::factory()->make([
        'type' => 'fixed',
        'value' => 50000,
    ]);

    $result = app(
        VoucherCalculationService::class
    )->calculate(
        300000,
        $voucher,
    );

    expect(
        $result->discountAmount
    )->toBe(50000.0);

    expect(
        $result->finalPrice
    )->toBe(250000.0);
});

it('returns original price without voucher', function () {

    $result = app(
        VoucherCalculationService::class
    )->calculate(
        100000,
        null,
    );

    expect(
        $result->discountAmount
    )->toBe(0.0);

    expect(
        $result->finalPrice
    )->toBe(100000.0);
});