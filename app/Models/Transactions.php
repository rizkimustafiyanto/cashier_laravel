<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use App\Enums\TransactionStatus;

class Transactions extends Model
{
    //
    use HasUuids, LogsActivity;

    protected $keyType = 'string';

    public $incrementing = false;
    
    protected $fillable = [
        'invoice_number',
        'patient_name',
        'patient_email',
        'patient_phone',
        'patient_gender',
        'patient_dob',
        'insurance_id',
        'insurance_name',
        'subtotal',
        'discount_total',
        'grand_total',
        'paid_at',
        'cashier_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'paid_at' => 'datetime',
            'status' => TransactionStatus::class,
        ];
    }

    /**
     * Transaction Items
     */

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id');
    }

    /**
     * Cashier Relationship
     */

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Log transaction
     */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('transactions')
            ->logOnly([
                'invoice_number',
                'patient_name',
                'patient_email',
                'patient_phone',
                'patient_gender',
                'patient_dob',
                'insurance_id',
                'insurance_name',
                'subtotal',
                'discount_total',
                'grand_total',
                'paid_at',
                'cashier_id',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
