<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Account routes
    Route::post('/accounts', [AccountController::class, 'create']);
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::get('/accounts/{account_id}', [AccountController::class, 'show']);

    // Transaction routes
    Route::post('/accounts/deposit', [TransactionController::class, 'deposit']);
    Route::post('/accounts/withdraw', [TransactionController::class, 'withdraw']);
    Route::post('/accounts/transfer', [TransactionController::class, 'transfer']);
    Route::get('/accounts/{account_id}/transactions', [TransactionController::class, 'transactions']);
});
