<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Enums\TransactionType;
class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'account_number', 'balance',
    ];
    public function hasSufficientBalance(float $amount): bool
    {
         return $amount > $this->balance;
    }

    public function createTransaction($type, $amount, $description = null): Transaction
    {
        return $this->transactions()->create([
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
        ]);
    }

    public function transfer(Account $toAccount, float $amount, string $description): void
    {
        DB::transaction(function () use ($toAccount, $amount, $description) {
            $this->balance -= $amount;
            $this->save();
            $toAccount->balance += $amount;
            $toAccount->save();
            $this->createTransaction(TransactionType::TRANSFER, $amount, $description);
            $toAccount->createTransaction(TransactionType::TRANSFER, $amount, $description);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
