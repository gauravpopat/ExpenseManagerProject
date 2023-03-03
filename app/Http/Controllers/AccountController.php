<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function list()
    {
        $accounts = Account::all();
        return response()->json([
            'message'   => 'Data',
            'data'      => $accounts
        ]);
    }

    public function insert(Request $request)
    {
        $validationForAccount = Validator::make($request->all(), [
            'account_name'   => 'required|max:40|alpha',
            'account_number' => 'required|numeric|unique:accounts,account_number',
            'user_id'        => 'numeric|required|exists:users,id'
        ]);

        if ($validationForAccount->fails()) {
            $errors = $validationForAccount->errors();
            return response()->json([
                'status'     => false,
                'message'    => 'Validation Error',
                'errors'     => $errors
            ]);
        }

        $account = Account::create($request->only(['account_name', 'account_number', 'user_id']));

        return response()->json([
            'status'         => true,
            'message'        => 'Account Created Successfully',
            'account'           => $account
        ], 200);
    }

    public function update($id, Request $request)
    {
        $validationForAccount = Validator::make($request->all(), [
            'account_name'   => 'required|max:40|alpha',
            'account_number' => 'required|numeric|unique:accounts,account_number',
        ]);

        if ($validationForAccount->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $validationForAccount->errors()
            ]);
        }

        $account = Account::find($id);
        if ($account) {
            $account->update($request->only(['account_name', 'account_number']));
            return response()->json([
                'status'    => true,
                'message'   => 'Record Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Record Not Found!',
            ]);
        }
    }

    public function show($id)
    {
        $account = Account::find($id);
        if($account){
            $account = $account->load('transactions','user','accountUsers');
            return response()->json([
                'message'   => 'Data',
                'accounts'      => $account
            ]);
        }
        else{
            return response()->json([
                'message'   => 'Account not found',
            ]);
        }
        
    }

    public function delete($id)
    {
        $account = Account::find($id);
        if ($account) {
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
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'No Account Found'
            ]);
        }
    }
}
