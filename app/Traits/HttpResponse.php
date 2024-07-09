<?php

namespace App\Traits;

use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

trait HttpResponse
{


/**
     * Create a new account for the authenticated user.
     *
     * @param array $data
     * @return Account
     */
    private function abortWithJson(string $message, int $code): JsonResponse{
        $payload = ['success' => false,'message'=> $message];
        return response()->json($payload, $code);
    }
    protected function createAccount(float $initial_balance): Account
    {
        return Account::create([
            'user_id' => Auth::id(),
            'account_number' => uniqid('ACC-'),
            'balance' => $initial_balance,
        ]);
    }
}
