<?php

namespace App\Http\Controllers;

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
            'type'                  => 'required|max:40|in:income,expense',
            'category'              => 'required|max:40',
            'amount'                => 'required|numeric',
            'account_user_id'       => 'required|numeric|exists:account_users,id',
            'account_id'            => 'required|numeric|exists:accounts,id'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $validate->errors()
            ]);
        }
        //Insert
        $transaction = Transaction::create($request->only(['type','category','amount','account_user_id','account_id']));
        return response()->json([
            'status'            => true,
            'message'           => 'Inserted Successfully',
            'data'              => $transaction
        ], 200);
    }


    //Update
    public function update($id, Request $request)
    {
        Transaction::findOrFail($id)->update($request->only('type','category','amount'));
        return response()->json([
            'status'   => true,
            'message'  => 'Data Updated Successfully',
        ]);
    }

    //Delete record from transaction
    public function delete($id)
    {
        Transaction::findOrFail($id)->delete();
        return response()->json([
            'status'    => true,
            'message'   => 'Data Deleted Successfully',
        ]);
    }

    //Get list of Transaction Table Records
    public function list()
    {
        $transaction = Transaction::all();
        return response()->json([
            'message'   => 'Data',
            'data'      => $transaction
        ]);
    }

    //Get Record from ID
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return response()->json([
            'message'   => 'Data',
            'data'      => $transaction
        ]);
    }
}