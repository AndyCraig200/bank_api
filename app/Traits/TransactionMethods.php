<?php
namespace App\Traits;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Enums\TransactionType;

trait TransactionMethods
{
    /**
     *
     * @param int $id
     * @return Account
     */
    protected function findUserAccountOrFail($id)
    {
        $account = Account::findOrFail($id);
        if (Auth::user()->can('view', $account)) {
            return $account;
        }else{
            abort_with_json(403, 'User not authorized for this account.');
        }
    }

    /**
     * Check if the account has sufficient balance for a transaction.
     *
     * @param Account $account
     * @param float $amount
     */
    protected function checkSufficientBalance(Account $account, $amount)
    {
        if ($amount > $account->balance) {
            abort_with_json(400, 'Insufficient balance for transaction.');
        }

    }

    /**
     * Create a transaction.
     *
     * @param Account $account
     * @param TransactionType $type
     * @param float $amount
     * @param string|null $description
     * @return Transaction
     */
    protected function createTransaction(Account $account, $type, $amount, $description = null)

    {
        $transaction = $account->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
        ]);
        return $transaction;
    }
}
