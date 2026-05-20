<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RoleSeeder;

it('cashier can access transaction page', function () {
    $this->seed(RoleSeeder::class);

    $user = User::factory()->create();

    $user->assignRole('cashier');

    $this->actingAs($user)
        ->get(
            route('transactions.create')
        )
        ->assertOk();
});

it('marketing cannot access cashier page', function () {
    $this->seed(RoleSeeder::class);

    $user = User::factory()->create();

    $user->assignRole('marketing');

    $this->actingAs($user)
        ->get(
            route('transactions.create')
        )
        ->assertForbidden();
});
