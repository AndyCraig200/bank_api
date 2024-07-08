<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transactions\DepositRequest;
use App\Http\Requests\Transactions\TransferRequest;
use App\Http\Requests\Transactions\WithdrawalRequest;

use Illuminate\Support\Facades\DB;
use App\Traits\TransactionMethods;
use App\Enums\TransactionType;

class TransactionController extends Controller
{
    use TransactionMethods;
    public function deposit(DepositRequest $request, $id)
    {
        $request->validated();

        $account = $this->findUserAccountOrFail($id);

        $account->balance += $request->amount;
        $account->save();

        $transaction = $this->createTransaction($account, TransactionType::DEPOSIT, $request->amount, $request->description);

        return response()->json($transaction, 201);
    }

    public function withdraw(WithdrawalRequest $request, $id)
    {
        $request->validated();

        $account = $this->findUserAccountOrFail($id);

        $this->checkSufficientBalance($account, $request->amount);

        $account->balance -= $request->amount;
        $account->save();

        $transaction = $this->createTransaction($account, TransactionType::WITHDRAWAL ,$request->amount, $request->description);

        return response()->json($transaction, 201);
    }

    public function transfer(TransferRequest $request)
    {
        $request->validated();

        $fromAccount = $this->findUserAccountOrFail($request->from_account_id);
        $toAccount = $this->findUserAccountOrFail($request->to_account_id);

        $this->checkSufficientBalance($fromAccount, $request->amount);

        DB::transaction(function () use ($fromAccount, $toAccount, $request) {
            $fromAccount->balance -= $request->amount;
            $fromAccount->save();

            $toAccount->balance += $request->amount;
            $toAccount->save();

            $transaction1 = $this->createTransaction($fromAccount, TransactionType::TRANSFER, $request->amount, $request->description);
            $transaction2 = $this->createTransaction($toAccount, TransactionType::TRANSFER, $request->amount, $request->description);

        });

        return response()->json(['message' => 'Transfer successful'], 200);
    }

    public function transactions($id)
    {
        $account = $this->findUserAccountOrFail($id);

        $transactions = $account->transactions()->get();

        return response()->json($transactions,200);
    }
}
