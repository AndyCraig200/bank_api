<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transactions\DepositRequest;
use App\Http\Requests\Transactions\TransferRequest;
use App\Http\Requests\Transactions\WithdrawalRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Enums\TransactionType;
use App\Traits\HttpResponse;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    use HttpResponse;
    public function deposit(DepositRequest $request): JsonResponse
    {
        $request->validated();

        $account = Account::findOrFail($request->account_id);
        if (Auth::user()->cannot('view', $account)) {
            return $this->abortWithJson("User not authorized for this account", 401);
        }

        $account->balance += $request->amount;
        $account->save();

        $transaction = $account->createTransaction(TransactionType::DEPOSIT, $request->amount, $request->description);

        return response()->json($transaction, 200);
    }

    public function withdraw(WithdrawalRequest $request): JsonResponse
    {
        $request->validated();

        $account = Account::findOrFail($request->account_id);
        if (Auth::user()->cannot('view', $account)) {
            return $this->abortWithJson("User not authorized for this account", 401);
        }

        if ($account->hasSufficientBalance($request->amount)){
            return $this->abortWithJson("Insufficient balance for transaction", 400);
        }

        $account->balance -= $request->amount;
        $account->save();

        $transaction = $account->createTransaction(TransactionType::WITHDRAWAL ,$request->amount, $request->description);

        return response()->json($transaction, 200);
    }

    public function transfer(TransferRequest $request): JsonResponse
    {
        $request->validated();

        $fromAccount = Account::findOrFail($request->from_account_id);
        $toAccount = Account::findOrFail($request->to_account_id);

        if (Auth::user()->cannot('view', $fromAccount) || Auth::user()->cannot('view', $toAccount)) {
            return $this->abortWithJson("User not authorize to transfer between these accounts.", 401);
        }

        if ($fromAccount->hasSufficientBalance($request->amount)){
            return $this->abortWithJson("Insufficient balance for transaction", 400);
        }

        $fromAccount->transfer($toAccount, $request->amount, $request->description);

        return response()->json(['message' => 'Transfer successful'], 200);
    }

    public function transactions(int $account_id): JsonResponse
    {
        $account = Account::findOrFail($account_id);
        if (Auth::user()->cannot('view', $account)) {
            return $this->abortWithJson("User not authorized for this account", 401);
        }

        $transactions = $account->transactions()->get();

        return response()->json($transactions,200);
    }
}
