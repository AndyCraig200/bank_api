<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $accounts = Account::factory()->count(3)->for($user)->hasTransactions(3)->create();

       //Transaction::factory()->count(3)->for($accounts)->create();



    }
}
