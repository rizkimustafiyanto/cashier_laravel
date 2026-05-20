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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            /**
             * Insurance Snapshot
             */

            $table->string('insurance_id');
            $table->string('insurance_name');

            /**
             * Voucher Logic
             */

            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 15, 2);
            $table->decimal('max_discount', 15, 2)->nullable();

            /**
             * Active Period
             */

            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);

            /**
             * Audit
             */

            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
