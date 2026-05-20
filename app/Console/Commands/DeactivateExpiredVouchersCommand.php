<?php

namespace App\Console\Commands;

use App\Services\Voucher\VoucherManagementService;
use Illuminate\Console\Command;

class DeactivateExpiredVouchersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'voucher:deactivate-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically deactivate expired vouchers';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $service = new VoucherManagementService();

        $count = $service->deactivateExpiredVouchers();

        $this->info("Deactivated {$count} expired voucher(s).");

        return self::SUCCESS;
    }
}
