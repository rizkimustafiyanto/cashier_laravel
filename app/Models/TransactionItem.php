<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    //

    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'transaction_id',
        'procedure_id',
        'procedure_name',
        'base_price',
        'voucher_type',
        'voucher_value',
        'discount_amount',
        'final_price',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'voucher_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'final_price' => 'decimal:2',
        ];
    }

    /**
     * Transaction Relationship
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }
}
