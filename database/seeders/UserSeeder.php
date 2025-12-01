<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'test@makeasite.gr'],
            [
                'name' => 'Test User',
                'email' => 'test@makeasite.gr',
                'password' => Hash::make('12341234'),
                'seed_capital' => 0,
                'median_monthly_income' => null,
                'income_last_verified_at' => null,
            ]
        );
    }
}
