<?php

declare(strict_types=1);

namespace App\DTOs;

use Carbon\Carbon;

readonly class VoucherData
{
    public function __construct(
        public string $insurance_id,
        public string $insurance_name,
        public string $type,
        public float $value,
        public ?float $max_discount = null,
        public ?Carbon $start_date = null,
        public ?Carbon $end_date = null,
        public bool $is_active = true,
        public ?string $created_by = null,
        public ?string $updated_by = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            insurance_id: $data['insurance_id'] ?? $data['insuranceId'] ?? '',
            insurance_name: $data['insurance_name'] ?? $data['insuranceName'] ?? '',
            type: $data['type'] ?? 'percentage',
            value: (float) ($data['value'] ?? 0),
            max_discount: isset($data['max_discount']) ? (float) $data['max_discount'] : (isset($data['maxDiscount']) ? (float) $data['maxDiscount'] : null),
            start_date: isset($data['start_date']) ? Carbon::parse($data['start_date']) : (isset($data['startDate']) ? Carbon::parse($data['startDate']) : null),
            end_date: isset($data['end_date']) ? Carbon::parse($data['end_date']) : (isset($data['endDate']) ? Carbon::parse($data['endDate']) : null),
            is_active: (bool) ($data['is_active'] ?? $data['isActive'] ?? true),
            created_by: $data['created_by'] ?? null,
            updated_by: $data['updated_by'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'insurance_id' => $this->insurance_id,
            'insurance_name' => $this->insurance_name,
            'type' => $this->type,
            'value' => $this->value,
            'max_discount' => $this->max_discount,
            'start_date' => $this->start_date?->startOfDay(),
            'end_date' => $this->end_date?->endOfDay(),
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
