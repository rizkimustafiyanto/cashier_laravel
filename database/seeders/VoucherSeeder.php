<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Active percentage voucher
        Voucher::create([
            'insurance_id' => 'bpjs',
            'insurance_name' => 'BPJS Kesehatan',
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => 500000,
            'start_date' => Carbon::now()->subMonth(),
            'end_date' => Carbon::now()->addMonth(),
            'is_active' => true,
            'created_by' => null,
        ]);

        // Active fixed amount voucher
        Voucher::create([
            'insurance_id' => 'asuransi_xyz',
            'insurance_name' => 'Asuransi XYZ',
            'type' => 'fixed',
            'value' => 100000,
            'start_date' => Carbon::now()->subDays(15),
            'end_date' => Carbon::now()->addDays(15),
            'is_active' => true,
            'created_by' => null,
        ]);

        // Inactive voucher
        Voucher::create([
            'insurance_id' => 'asuransi_abc',
            'insurance_name' => 'Asuransi ABC',
            'type' => 'percentage',
            'value' => 5,
            'start_date' => Carbon::now()->subMonth(),
            'end_date' => Carbon::now()->addMonth(),
            'is_active' => false,
            'created_by' => null,
        ]);

        // Expired voucher
        Voucher::create([
            'insurance_id' => 'bpjs',
            'insurance_name' => 'BPJS Kesehatan',
            'type' => 'percentage',
            'value' => 15,
            'max_discount' => 750000,
            'start_date' => Carbon::now()->subMonths(3),
            'end_date' => Carbon::now()->subMonth(),
            'is_active' => true,
            'created_by' => null,
        ]);

        // Future voucher
        Voucher::create([
            'insurance_id' => 'asuransi_xyz',
            'insurance_name' => 'Asuransi XYZ',
            'type' => 'fixed',
            'value' => 250000,
            'start_date' => Carbon::now()->addMonth(),
            'end_date' => Carbon::now()->addMonths(2),
            'is_active' => true,
            'created_by' => null,
        ]);
    }
}
