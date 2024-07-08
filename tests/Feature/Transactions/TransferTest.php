<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_transfer_money_between_accounts()
    {
        $user = User::factory()->create();
        $fromAccount = Account::factory()->create(['user_id' => $user->id, 'balance' => 1000]);
        $toAccount = Account::factory()->create(['user_id' => $user->id, 'balance' => 500]);

        $response = $this->actingAs($user)->postJson("/api/accounts/transfer", [
            'from_account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'amount' => 300,
            'description' => 'Test transfer',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('accounts', ['id' => $fromAccount->id, 'balance' => 700]);
        $this->assertDatabaseHas('accounts', ['id' => $toAccount->id, 'balance' => 800]);
        $this->assertDatabaseHas('transactions', ['account_id' => $fromAccount->id, 'type' => 'transfer', 'amount' => 300]);
        $this->assertDatabaseHas('transactions', ['account_id' => $toAccount->id, 'type' => 'transfer', 'amount' => 300]);
    }

    public function test_user_cannot_transfer_more_than_balance()
    {
        $user = User::factory()->create();
        $fromAccount = Account::factory()->create(['user_id' => $user->id, 'balance' => 1000]);
        $toAccount = Account::factory()->create(['user_id' => $user->id, 'balance' => 500]);

        $response = $this->actingAs($user)->postJson("/api/accounts/transfer", [
            'from_account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'amount' => 1500,
            'description' => 'Test over-transfer',
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseHas('accounts', ['id' => $fromAccount->id, 'balance' => 1000]);
        $this->assertDatabaseMissing('transactions', ['account_id' => $fromAccount->id, 'type' => 'transfer', 'amount' => 1500]);
    }
}
