<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            /**
             * Transaction Reference
             */

            $table->foreignUuid('transaction_id')->constrained()->cascadeOnDelete();

            /**
             * Procedure Snapshot
             */

            $table->string('procedure_id');
            $table->string('procedure_name');

            /**
             * Pricing
             */

            $table->decimal('base_price', 15, 2);

            /**
             * Voucher Snapshot
             */
            
            $table->string('voucher_type')->nullable();
            $table->decimal('voucher_value', 15, 2)->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);

            /**
             * Final Price
             */

            $table->decimal('final_price', 15, 2);


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
