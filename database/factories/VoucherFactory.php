<?php

namespace Database\Factories;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'insurance_id' => fake()->bothify('INS-###'),
            'insurance_name' => fake()->company(),
            'type' => 'percentage',
            'value' => fake()->randomFloat(2, 5, 50),
            'max_discount' => null,
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    /**
     * State for active percentage vouchers
     */
    public function activePercentage(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'percentage',
                'value' => fake()->randomFloat(2, 1, 50),
                'max_discount' => fake()->boolean(70) ? fake()->randomFloat(2, 100000, 1000000) : null,
                'is_active' => true,
                'start_date' => Carbon::now()->subDay()->toDateString(),
                'end_date' => Carbon::now()->addMonth()->toDateString(),
            ];
        });
    }

    /**
     * State for active fixed vouchers
     */
    public function activeFixed(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'fixed',
                'value' => fake()->randomFloat(2, 50000, 500000),
                'is_active' => true,
                'start_date' => Carbon::now()->subDay()->toDateString(),
                'end_date' => Carbon::now()->addMonth()->toDateString(),
            ];
        });
    }

    /**
     * State for expired vouchers
     */
    public function expired(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
                'start_date' => Carbon::now()->subMonths(2)->toDateString(),
                'end_date' => Carbon::now()->subDay()->toDateString(),
            ];
        });
    }

    /**
     * State for inactive vouchers
     */
    public function inactive(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * State for future vouchers
     */
    public function future(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
                'start_date' => Carbon::now()->addMonth()->toDateString(),
                'end_date' => Carbon::now()->addMonths(2)->toDateString(),
            ];
        });
    }
}
