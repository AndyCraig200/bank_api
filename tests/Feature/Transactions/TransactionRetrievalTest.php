<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRetrievalTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_retrieve_transactions()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);
        $transactions = Transaction::factory()->count(3)->create(['account_id' => $account->id]);

        $response = $this->actingAs($user)->getJson("/api/accounts/{$account->id}/transactions");

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_user_cannot_retrieve_transactions_of_other_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $otherUser->id]);
        $transactions = Transaction::factory()->count(3)->create(['account_id' => $account->id]);

        $response = $this->actingAs($user)->getJson("/api/accounts/{$account->id}/transactions");

        $response->assertStatus(401);
    }
}
