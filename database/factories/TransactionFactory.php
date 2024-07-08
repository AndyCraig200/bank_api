<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Account;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'type' => $this->faker->randomElement(['deposit', 'withdrawal', 'transfer']),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'description' => $this->faker->sentence,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
