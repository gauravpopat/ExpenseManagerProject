<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseTrait;

class TransactionController extends Controller
{
    use ResponseTrait;
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
            $this->returnResponse(false,"No Transaction Data!!");
        }
    }

    //Insert
    public function insert(Request $request)
    {
        //Validation
        $validation = Validator::make($request->all(), [
            'type'                  => 'required|in:income,expense',
            'category'              => 'required|max:40',
            'amount'                => 'required|numeric',
            'account_user_id'       => 'required|numeric|exists:account_users,id',
            'account_id'            => 'required|numeric|exists:accounts,id'
        ]);

        $this->ValidationErrorsResponse($validation);
        //Insert
        Transaction::create($request->only(['type', 'category', 'amount', 'account_user_id', 'account_id']));
        $this->returnResponse(true,"Inserted Successfully");
    }

    //Update
    public function update(Request $request)
    {
        //Validation
        $validation = Validator::make($request->all(), [
            'id'                    => 'required|exists:transactions,id',
            'type'                  => 'required|in:income,expense',
            'category'              => 'required|max:40',
            'amount'                => 'required|numeric'
        ]);

        $this->ValidationErrorsResponse($validation);

        Transaction::findOrFail($request->id)->update($request->only('type', 'category', 'amount'));
        $this->returnResponse(true,"Data Updated Successfully");
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
        $this->returnResponse(true,"Transaction Deleted Successfully");
    }
}
