<?php

namespace App\Http\Controllers;
use App\Http\Requests\Accounts\CreateAccountRequest;
use App\Traits\AccountMethods;

class AccountController extends Controller
{
    use AccountMethods;
    public function create(CreateAccountRequest $request)
    {
        $request->validated();
        $account = $this->createAccount($request->initial_balance);

        return response()->json($account, 201);
    }

    public function show($id)
    {
        $account = $this->findUserAccountOrFail($id);
        return response()->json($account);
    }

    public function index()
    {
        $accounts = $this->getUserAccounts();
        return response()->json($accounts);
    }
}
