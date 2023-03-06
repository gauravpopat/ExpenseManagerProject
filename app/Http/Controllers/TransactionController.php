<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //Get list of Transaction Table Records
    public function list()
    {
        $user = User::where('id', Auth()->user()->id)->get();
        $transactions = $user->load('transactions');
        if ($transactions) {
            return response()->json([
                'message'            => 'Transaction Record',
                'transaction'        => $transactions,
            ]);
        } else {
            return response()->json([
                'message'   => 'No transaction Data Found',
            ]);
        }
    }

    //Insert
    public function insert(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'type'                  => 'required|in:income,expense',
            'category'              => 'required|max:40',
            'amount'                => 'required|numeric',
            'account_user_id'       => 'required|numeric|exists:account_users,id',
            'account_id'            => 'required|numeric|exists:accounts,id'
        ]);

        //Validation Error
        if ($validate->fails()) {
            $errors = $validate->errors();
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $errors
            ]);
        }

        //Insert
        $transaction = Transaction::create($request->only(['type', 'category', 'amount', 'account_user_id', 'account_id']));
        return response()->json([
            'status'            => true,
            'message'           => 'Inserted Successfully',
            'data'              => $transaction
        ], 200);
    }

    //Update
    public function update(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'id'                    => 'required|exists:transactions,id',
            'type'                  => 'required|in:income,expense',
            'category'              => 'required|max:40',
            'amount'                => 'required|numeric'
        ]);

        //Validation Error
        if ($validate->fails()) {
            $errors = $validate->errors();
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $errors
            ]);
        }

        $transaction = Transaction::findOrFail($request->id);
        $transaction->update($request->only('type', 'category', 'amount'));
        return response()->json([
            'status'   => true,
            'message'  => 'Data Updated Successfully',
        ]);
    }

    //Get Record from ID
    public function show($id)
    {
        $transactions = Transaction::findOrFail($id);
        $transactions = $transactions->load('account', 'accountUsers');
        return response()->json([
            'message'           => 'Transactions Data',
            'transactions'      => $transactions
        ]);
    }

    //Delete record from transaction
    public function delete($id)
    {
        Transaction::findOrFail($id)->delete();
        return response()->json([
            'status'    => true,
            'message'   => 'Transaction Deleted Successfully',
        ]);
    }
}
