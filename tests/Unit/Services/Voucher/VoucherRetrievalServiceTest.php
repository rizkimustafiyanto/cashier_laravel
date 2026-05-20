<?php

namespace Tests\Unit\Services\Voucher;

use App\Models\Voucher;
use App\Services\Voucher\VoucherRetrievalService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherRetrievalServiceTest extends TestCase
{
    use RefreshDatabase;

    private VoucherRetrievalService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new VoucherRetrievalService();
    }

    /**
     * Test get active vouchers for insurance
     */
    public function test_get_active_vouchers_for_insurance(): void
    {
        $insurance = 'bpjs';

        Voucher::factory()
            ->count(3)
            ->create([
                'insurance_id' => $insurance,
                'is_active' => true,
                'start_date' => now()->subDay(),
                'end_date' => now()->addDay(),
            ]);

        Voucher::factory()->create([
            'insurance_id' => 'other',
            'is_active' => true,
        ]);

        $vouchers = $this->service->getActiveVouchersForInsurance($insurance);

        $this->assertCount(3, $vouchers);
        $this->assertTrue($vouchers->every(fn(Voucher $v) => $v->insurance_id === $insurance));
    }

    /**
     * Test check if voucher is applicable
     */
    public function test_check_voucher_applicability(): void
    {
        $activeVoucher = Voucher::factory()->create([
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
        ]);

        $inactiveVoucher = Voucher::factory()->create([
            'is_active' => false,
        ]);

        $expiredVoucher = Voucher::factory()->create([
            'is_active' => true,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDay(),
        ]);

        $this->assertTrue($this->service->isVoucherApplicable($activeVoucher));
        $this->assertFalse($this->service->isVoucherApplicable($inactiveVoucher));
        $this->assertFalse($this->service->isVoucherApplicable($expiredVoucher));
    }

    /**
     * Test get expiring vouchers
     */
    public function test_get_expiring_vouchers(): void
    {
        Voucher::factory()->create([
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(3),
        ]);

        Voucher::factory()->create([
            'is_active' => true,
            'start_date' => now()->subDay(),
            'end_date' => now()->addMonth(),
        ]);

        $expiring = $this->service->getExpiringVouchers();

        $this->assertGreaterThanOrEqual(1, $expiring->count());
    }
}
