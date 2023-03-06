<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ValidationTrait;

class AccountController extends Controller
{
    use ValidationTrait;
    public function list()
    {
        $accounts = User::findOrFail(auth()->user()->id)->load('accounts');
        return response()->json([
            'message'   => 'Accounts',
            'accounts'  => $accounts
        ]);
    }

    public function insert(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'account_name'   => 'required|max:40|string',
            'account_number' => 'required|numeric|digits:12|unique:accounts,account_number',
            'user_id'        => 'numeric|required|exists:users,id'
        ]);

        $this->ValidationErrorsResponse($validation);

        $account = Account::create($request->only(['account_name', 'account_number', 'user_id']));

        return response()->json([
            'status'         => true,
            'message'        => 'Account Created Successfully',
            'account'        => $account
        ], 200);
    }

    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'             => 'required|exists:accounts,id',
            'account_name'   => 'required|max:40|string',
            'account_number' => 'required|numeric|digits:12|unique:accounts,account_number',
        ]);

        $this->ValidationErrorsResponse($validation);

        Account::findOrFail($request->id)->update($request->only(['account_name', 'account_number']));
        return response()->json([
            'status'    => true,
            'message'   => 'Record Updated Successfully',
        ]);
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        $account = $account->load('transactions', 'user', 'accountUsers');
        return response()->json([
            'message'       => 'Account',
            'account'       => $account
        ]);
    }

    public function delete($id)
    {
        $account = Account::findOrFail($id);
        if ($account->is_default == true) {
            return response()->json([
                'status'    => false,
                'message'   => 'Deletation not allowed for default account'
            ]);
        } else {
            $account->delete();
            return response()->json([
                'status'    => true,
                'message'   => 'Account Deleted Successfully...'
            ]);
        }
    }
}
