<?php

namespace App\Traits;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

trait AccountMethods
{
    /**
     * Create a new account for the authenticated user.
     *
     * @param array $data
     * @return Account
     */
    protected function createAccount(float $initial_balance): Account
    {
        return Account::create([
            'user_id' => Auth::id(),
            'account_number' => uniqid('ACC-'),
            'balance' => $initial_balance,
        ]);
    }

    /**
     * Find an account by ID and check if it belongs to the authenticated user.
     *
     * @param int $id
     * @return Account
     */
    protected function findUserAccountOrFail(int $id): Account
    {
        $account = Account::findOrFail($id);
        if (Auth::user()->can('view', $account)) {
            return $account;
        }else{
            abort_with_json(403, 'User not authorized for this account.');
        }
    }

    /**
     * Get all accounts for the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUserAccounts()
    {
        return Account::where('user_id', Auth::id())->get();
    }
}

