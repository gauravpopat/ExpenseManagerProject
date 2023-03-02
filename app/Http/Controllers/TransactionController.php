<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //Insert
    public function insert(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'type'                  => 'required|max:40|in:income,expense',
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
    public function update($id, Request $request)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            Transaction::find($id)->update($request->only('type', 'category', 'amount'));
            return response()->json([
                'status'   => true,
                'message'  => 'Data Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status'   => false,
                'message'  => 'Transaction not found',
            ]);
        }
    }

    //Delete record from transaction
    public function delete($id)
    {
        $transaction = Transaction::find($id);
        if ($transaction) {
            Transaction::find($id)->delete();
            return response()->json([
                'status'    => true,
                'message'   => 'Transaction Deleted Successfully',
            ]);
        } else {
            return response()->json([
                'status'    => false,
                'message'   => 'Transaction not found',
            ]);
        }
    }

    //Get list of Transaction Table Records
    public function list()
    {
        $transactions = Transaction::all();
        return response()->json([
            'message'   => 'Transactions',
            'data'      => $transactions
        ]);
    }

    //Get Record from ID
    public function show($id)
    {
        $getTransaction = Transaction::find($id);
        if($getTransaction){
            $transaction = Transaction::find($id);
            return response()->json([
                'message'   => 'Transaction',
                'data'      => $transaction
            ]);
        }
        else{
            return response()->json([
                'message'   => 'Transaction Not Found'
            ]);
        }
        
    }
}
