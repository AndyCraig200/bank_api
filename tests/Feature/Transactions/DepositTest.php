<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepositTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_deposit_money()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'balance' => 1000]);

        $response = $this->actingAs($user)->postJson("/api/accounts/{$account->id}/deposit", [
            'amount' => 500,
            'description' => 'Test deposit',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'balance' => 1500]);
        $this->assertDatabaseHas('transactions', ['account_id' => $account->id, 'type' => 'deposit', 'amount' => 500]);
    }
}
