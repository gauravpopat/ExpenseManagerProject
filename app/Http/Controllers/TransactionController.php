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
        $transactions = $user->load('transactions','userAccounts','accounts');
        if ($transactions) {
            return $this->returnResponse(true, "Transaction Record", $transactions);
        } else {
            return $this->returnResponse(false, "No Transaction Data!!");
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
        if ($validation->fails())
            return $this->ValidationErrorsResponse($validation);
        //Insert
        Transaction::create($request->only(['type', 'category', 'amount', 'account_user_id', 'account_id']));
        return $this->returnResponse(true, "Inserted Successfully");
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

        if ($validation->fails())
            return $this->ValidationErrorsResponse($validation);

        Transaction::findOrFail($request->id)->update($request->only('type', 'category', 'amount'));
        return $this->returnResponse(true, "Data Updated Successfully");
    }

    //Get Record from ID
    public function show($id)
    {
        $transactions = Transaction::findOrFail($id);
        return $this->returnResponse(true, "Transaction Data", $transactions);
    }

    //Delete record from transaction
    public function delete($id)
    {
        Transaction::findOrFail($id)->delete();
        return $this->returnResponse(true, "Transaction Deleted Successfully");
    }
}
