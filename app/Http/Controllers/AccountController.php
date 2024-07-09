<?php

namespace App\Http\Controllers;
use App\Http\Requests\Accounts\CreateAccountRequest;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\Account;

class AccountController extends Controller
{
    use HttpResponse;
    public function create(CreateAccountRequest $request): JsonResponse
    {
        $request->validated();
        if (Auth::user()->cannot("create", Account::class)) {
            return $this->abortWithJson("User cannot create account", 403);
        }

        $account =  Account::create([
            'user_id' => Auth::id(),
            'account_number' => uniqid('ACC-'),
            'balance' => $request->initial_balance,
        ]);

        return response()->json($account, 201);
    }

    public function show(int $account_id): JsonResponse
    {
        $account = Account::findOrFail($account_id);
        if (Auth::user()->cannot('view', $account)) {
            return $this->abortWithJson("User not authorized for this account", 401);
        }
        return response()->json($account, 200);
    }

    public function index(): JsonResponse
    {
        $accounts =  Account::where('user_id', Auth::id())->get();
        return response()->json($accounts, 200);
    }
}
