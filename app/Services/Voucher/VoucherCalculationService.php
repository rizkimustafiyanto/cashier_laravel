<?php

declare(strict_types=1);

namespace App\Services\Voucher;

use App\DTOs\VoucherCalculationResultData;
use App\Enums\VoucherTypes;
use App\Models\Voucher;

class VoucherCalculationService
{
    public function calculate(
      float $price,
      ?Voucher $voucher = null,
      ): VoucherCalculationResultData
      {
        /**
         * No Voucher Case
         */
        if (!$voucher) {
            return new VoucherCalculationResultData(
                basePrice: $price,
                discountAmount: 0.00,
                finalPrice: $price,
            );
        }

        /**
         * Percentage Voucher
         */

        if ($voucher->type === VoucherTypes::Percentage->value) {
            $discount = (
              $price * (float)$voucher->value
            ) / 100;

            /**
             * Max Discount
             */

            if ($voucher->max_discount !== null) {
                $discount = min($discount, (float)$voucher->max_discount);
            }

            return new VoucherCalculationResultData(
                basePrice: $price,
                discountAmount: round($discount, 2),
                finalPrice: round($price - $discount, 2),
            );
        }

        /**
         * Fixed Amount Voucher
         */

        if ($voucher->type === VoucherTypes::FixedAmount->value) {
            $discountAmount = min((float)$voucher->value, $price);
            $finalPrice = round($price - $discountAmount, 2);

            return new VoucherCalculationResultData(
                basePrice: $price,
                discountAmount: $discountAmount,
                finalPrice: $finalPrice,
            );
        }

        /**
         * Unknown Voucher Type
         */

        return new VoucherCalculationResultData(
            basePrice: $price,
            discountAmount: 0.00,
            finalPrice: $price,
        );
    }
}
