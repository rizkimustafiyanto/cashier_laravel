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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            /**
             * Invoice
             */

            $table->string('invoice_number')->unique();

            /**
             * Patient
             */

            $table->string('patient_name');
            $table->string('patient_email')->nullable();
            $table->string('patient_phone')->nullable();
            $table->enum('patient_gender', ['male', 'female', 'other'])->nullable();
            $table->date('patient_dob')->nullable();

            /**
             * Insurance Snapshot
             */

            $table->string('insurance_id')->nullable();
            $table->string('insurance_name')->nullable();

            /**
             * Payment Summary
             */

            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            /**
             * Status
             */

            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('draft');

            /**
             * Audit Cashier
             */

            $table->foreignUuid('cashier_id')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
