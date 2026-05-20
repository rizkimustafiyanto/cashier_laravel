<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Cashier
         */

        $cashier = User::query()->create([
            'name' => 'Cashier',
            'email' => 'cashier@example.com',
            'password' => bcrypt('password'),
        ]);

        $cashier->assignRole('cashier');

        /**
         * Marketing
         */

        $marketing = User::query()->create([
            'name' => 'Marketing',
            'email' => 'marketing@example.com',
            'password' => bcrypt('password'),
        ]);

        $marketing->assignRole('marketing');
    }
}
