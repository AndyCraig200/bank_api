<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_account()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/accounts', [
            'initial_balance' => 1000,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'balance' => 1000,
        ]);
    }

    public function test_user_can_view_their_account()
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $account->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_view_other_users_account()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->getJson("/api/accounts/{$account->id}");

        $response->assertStatus(403);
        $response->assertJson(['error' => 'User not authorized for this account.']);
    }

    public function test_user_can_list_their_accounts()
    {
        $user = User::factory()->create();
        $accounts = Account::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/accounts');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }
}
