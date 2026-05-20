<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Voucher extends Model
{
    use HasUuids, SoftDeletes, HasFactory, LogsActivity;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'insurance_id',
        'insurance_name',
        'type',
        'value',
        'max_discount',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Acitivity Log
     */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('vouchers')
            ->logOnly([
                'insurance_id',
                'insurance_name',
                'type',
                'value',
                'max_discount',
                'start_date',
                'end_date',
                'is_active',
                'created_by',
            ])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

}
