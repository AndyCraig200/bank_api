<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_withdraw_money()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'balance' => 1000]);

        $response = $this->actingAs($user)->postJson("/api/accounts/withdraw", [
            'account_id' => $account->id,
            'amount' => 500,
            'description' => 'Test withdrawal',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'balance' => 500]);
        $this->assertDatabaseHas('transactions', ['account_id' => $account->id, 'type' => 'withdrawal', 'amount' => 500]);
    }

    public function test_user_cannot_withdraw_more_than_balance()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'balance' => 1000]);

        $response = $this->actingAs($user)->postJson("/api/accounts/withdraw", [
            'account_id' => $account->id,
            'amount' => 1500,
            'description' => 'Test overdraw',
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'balance' => 1000]);
        $this->assertDatabaseMissing('transactions', ['account_id' => $account->id, 'type' => 'withdrawal', 'amount' => 1500]);
    }
}
