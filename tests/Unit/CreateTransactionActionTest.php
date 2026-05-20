<?php

declare(strict_types=1);

use App\Actions\Transaction\CreateTransactionAction;
use App\DTOs\CreateTransactionData;
use App\DTOs\TransactionItemData;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(
    RefreshDatabase::class,
);

it('creates transaction successfully', function () {

    Http::fake([
        '*/insurances' => Http::response([
            'data' => [
                [
                    'id' => 'INS-1',
                    'name' => 'BPJS',
                ],
            ],
        ]),
        '*/procedures/*/prices' => Http::response([
            'data' => [
                'price' => 100000,
            ],
        ]),
        '*' => Http::response([
            'access_token' => 'test-token',
        ]),

    ]);

    $cashier = User::factory()->create();

    $voucher = Voucher::factory()->create([
        'insurance_id' => 'INS-1',
        'type' => 'percentage',
        'value' => 10,
    ]);

    $transaction = app(
        CreateTransactionAction::class
    )->execute(
        new CreateTransactionData(
            patientName: 'John Doe',
            patientEmail: 'john.doe@example.com',
            patientPhone: '081234567890',
            patientGender: 'male',
            patientDob: '1990-01-01',
            insuranceId: 'INS-1',
            items: [
                new TransactionItemData(
                    procedureId: 'PROC-1',
                    procedureName: 'Blood Test',
                ),
            ],
            cashierId: $cashier->id,
        )
    );

    expect(
        $transaction->items
    )->toHaveCount(1);

    expect(
        (float) $transaction->grand_total
    )->toBe(90000.0);
});
