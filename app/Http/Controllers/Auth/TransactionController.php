<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //Transaction CRUD

    //Insert
    public function insert(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'type'                  => 'required | in:income,expense',
            'category'              => 'required',
            'amount'                => 'required | numeric',
            'account_user_id'       => 'numeric | required | exists:account_users,id',
            'account_id'            => 'numeric | required | exists:accounts,id'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return $validate->errors();
        }

        //Insert
        $transaction = Transaction::create([
            'type'              => $request->type,
            'category'          => $request->category,
            'amount'            => $request->amount,
            'account_user_id'   => $request->account_user_id,
            'account_id'        => $request->account_id,
        ]);

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

        // If request is null then it will store the old value in update otherwise new value(req value)

        if ($request->type == null) {
            $type = $transaction->type;
        } else {
            $type = $request->type;
        }

        if ($request->category == null) {
            $category = $transaction->category;
        } else {
            $category = $request->category;
        }

        if ($request->amount == null) {
            $amount = $transaction->amount;
        } else {
            $amount = $request->amount;
        }

        $transaction->update([
            'type'         => $type,
            'category'     => $category,
            'amount'       => $amount
        ]);
        return $transaction;
    }

    //Delete record from transaction
    public function delete($id)
    {
        $transaction = Transaction::find($id);
        $transaction->delete();
        return "Transaction Deleted Successfully";
    }


    //Get list of Transaction Table Records
    public function list()
    {
        return Transaction::all();
    }

    //Get Record from ID
    public function show($id)
    {
        return Transaction::find($id);
    }
}
