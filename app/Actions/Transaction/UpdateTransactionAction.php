<?php

namespace App\Actions\Transaction;

use App\DTOs\UpdateTransactionData;
use App\Enums\TransactionStatus;
use App\Exceptions\Business\TransactionPaidException;
use App\Models\Transactions;
use App\Models\TransactionItem;
use App\Models\Voucher;
use App\Services\API\RecruitmentApiService;
use App\Services\Voucher\VoucherCalculationService;
use Illuminate\Support\Facades\DB;

class UpdateTransactionAction
{
    public function __construct(
        private readonly RecruitmentApiService $recruitmentApiService,
        private readonly VoucherCalculationService $voucherCalculationService,
    ) {}

    /**
     * Update Transaction
     */

    public function execute(
        Transactions $transaction,
        UpdateTransactionData $data,
    ): Transactions {

        /**
         * Prevent update paid transaction
         */

        if (
            $transaction->status ===
            TransactionStatus::Paid
        ) {

            throw new TransactionPaidException(
                'Paid transaction cannot be updated.',
            );
        }

        return DB::transaction(function () use (
            $transaction,
            $data,
        ) {

            /**
             * Find Insurance
             */

            $insurance = collect(
                $this->recruitmentApiService
                    ->getInsurance()
            )->firstWhere(
                'id',
                $data->insuranceId,
            );

            /**
             * Resolve Voucher
             */

            $voucher = $this->resolveVoucherForInsurance(
                $data->insuranceId,
            );

            /**
             * Update Transaction Snapshot
             */

            $transaction->update([
                'patient_name' => $data->patientName,

                'patient_email' => $data->patientEmail,

                'patient_phone' => $data->patientPhone,

                'patient_gender' => $data->patientGender,

                'patient_dob' => $data->patientDob,

                'insurance_id' => $insurance['id'] ?? null,

                'insurance_name' => $insurance['name'] ?? null,
            ]);

            /**
             * Delete Old Items
             */

            $transaction->items()->delete();

            $subtotal = 0;

            $discountTotal = 0;

            /**
             * Recreate Transaction Items
             */

            foreach ($data->items as $item) {

                /**
                 * Fetch Price
                 */

                $priceData = $this
                    ->recruitmentApiService
                    ->getProcedurePrices(
                        $item->procedureId,
                    );

                $price = (float) (
                    data_get(
                        $priceData,
                        'price',
                    ) ??

                    data_get(
                        $priceData,
                        'unit_price',
                    ) ??

                    0
                );

                /**
                 * Voucher Calculation
                 */

                $result = $this
                    ->voucherCalculationService
                    ->calculate(
                        price: $price,
                        voucher: $voucher,
                    );

                /**
                 * Recreate Item Snapshot
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

                $subtotal +=
                    $result->basePrice;

                $discountTotal +=
                    $result->discountAmount;
            }

            /**
             * Update Totals
             */

            $transaction->update([
                'subtotal' => $subtotal,

                'discount_total' => $discountTotal,

                'grand_total' => (
                    $subtotal - $discountTotal
                ),
            ]);

            return $transaction->load([
                'items',
                'cashier',
            ]);
        });
    }

    /**
     * Resolve Voucher For Insurance
     */

    private function resolveVoucherForInsurance(
        ?string $insuranceId,
    ): ?Voucher {

        if (empty($insuranceId)) {
            return null;
        }

        $insurance = collect(
            $this->recruitmentApiService
                ->getInsurance()
        )->firstWhere(
            'id',
            $insuranceId,
        );

        $insuranceName = data_get(
            $insurance,
            'name',
        );

        return Voucher::query()
            ->where(
                'is_active',
                true,
            )
            ->where(
                'start_date',
                '<=',
                now(),
            )
            ->where(
                'end_date',
                '>=',
                now(),
            )
            ->where(function ($query) use (
                $insuranceId,
                $insuranceName,
            ) {

                $query->where(
                    'insurance_id',
                    $insuranceId,
                );

                if ($insuranceName) {

                    $query->orWhere(
                        'insurance_name',
                        $insuranceName,
                    );
                }
            })
            ->first();
    }
}