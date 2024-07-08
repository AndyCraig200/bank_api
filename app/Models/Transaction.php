<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TransactionType;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id', 'type', 'amount', 'description',
    ];
    protected $casts = [
        'type' => TransactionType::class,
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
