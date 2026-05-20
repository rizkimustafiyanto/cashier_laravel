<?php

namespace App\Actions\Transaction;

use App\DTOs\CreateTransactionData;
use App\Enums\TransactionStatus;
use App\Models\Transactions;
use App\Models\TransactionItem;
use App\Models\Voucher;
use App\Services\API\RecruitmentApiService;
use App\Services\Voucher\VoucherCalculationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateTransactionAction
{
    public function __construct(
        private readonly RecruitmentApiService $recruitmentApiService,
        private readonly VoucherCalculationService $voucherCalculationService,
    ) {}

    /**
     * Create Transaction
     */

    public function execute(CreateTransactionData $data): Transactions
    {
      return DB::transaction(function () use ($data) {
        /**
         * Find Insurance
         */

        $insurance = collect($this->recruitmentApiService->getInsurance())->firstWhere('id', $data->insuranceId);

        /**
         * Active Voucher
         */

        $voucher = $this->resolveVoucherForInsurance($data->insuranceId);

        /**
          * Create Transaction
          */

      
        $transaction = Transactions::query()->create([
          'invoice_number' => $this->generateInvoice(),
          'patient_name' => $data->patientName,
          'patient_email' => $data->patientEmail,
          'patient_phone' => $data->patientPhone,
          'patient_gender' => $data->patientGender,
          'patient_dob' => $data->patientDob,
          'insurance_id' => $insurance['id'] ?? null,
          'insurance_name' => $insurance['name'] ?? null,
          'subtotal' => 0,
          'discount_total' => 0,
          'grand_total' => 0,
          'cashier_id' => $data->cashierId,
          'status' => TransactionStatus::Draft,
        ]);

        $subtotal = 0;
        $discountTotal = 0;

        /**
         * Create Transaction Items
         */

        foreach ($data->items as $item) {
          /* Fetch Price */

          $priceData = $this->recruitmentApiService->getProcedurePrices($item->procedureId);

          $price = (float) (
            data_get($priceData, 'price') ??
            data_get($priceData, 'unit_price') ??
            0
          );

          /**
           * Voucher Calculation
           */

          $result = $this->voucherCalculationService->calculate(
            price: $price,
            voucher: $voucher,
          );

          /**
           * Create Item Snapshot
           */

          TransactionItem::query()->create([
            'transaction_id' => $transaction->id,
            'procedure_id' => $item->procedureId,
            'procedure_name' => $item->procedureName,
            'base_price' => $result->basePrice,
            'voucher_type' => $voucher->type ?? null,
            'voucher_value' => $voucher->value ?? null,
            'discount_amount' => $result->discountAmount,
            'final_price' => $result->finalPrice,
          ]);

          $subtotal += $result->basePrice;
          $discountTotal += $result->discountAmount;
        }

        /**
         * Update Transaction Total
         */

        $transaction->update([
          'subtotal' => $subtotal,
          'discount_total' => $discountTotal,
          'grand_total' => $subtotal - $discountTotal,
        ]);

        return $transaction->load([
          'items',
          'cashier',
        ]);
      });
    }

    private function resolveVoucherForInsurance(?string $insuranceId): ?Voucher
    {
        if (empty($insuranceId)) {
            return null;
        }

        $insurance = collect($this->recruitmentApiService->getInsurance())
            ->firstWhere('id', $insuranceId);

        $insuranceName = data_get($insurance, 'name');

        return Voucher::query()
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query) use ($insuranceId, $insuranceName) {
                $query->where('insurance_id', $insuranceId);

                if ($insuranceName) {
                    $query->orWhere('insurance_name', $insuranceName);
                }
            })
            ->first();
    }

    /**
     * Generate Invoice Number
     */

    private function generateInvoice(): string
    {
      return 'INV-'. now()->format('Ymd') . Str::upper(Str::random(8));
    }
}
