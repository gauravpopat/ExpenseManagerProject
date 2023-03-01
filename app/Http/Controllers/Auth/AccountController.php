<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function update($id, Request $request)
    {
        $account = Account::find($id);

        // If request is null then it will store the old value in update otherwise new value(req value)

        if ($request->account_name == null) {
            $account_name = $account->account_name;
        } else {
            $account_name = $request->account_name;
        }

        if ($request->account_number == null) {
            $account_number = $account->account_number;
        } else {
            $account_number = $request->account_number;
        }

        $account->update([
            'account_name'   => $account_name,
            'account_number' => $account_number,
        ]);
        return $account;
    }

    public function delete($id)
    {
        $account = Account::find($id);

        if ($account->is_default == true) {
            return "Deletation not allowed for default account";
        } else {
            $account->delete();
            return "Account Deleted Successfully";
        }
    }

    public function show($id)
    {
        return Account::find($id);
    }

    public function insert(Request $request)
    {
        $validationForAccount = Validator::make($request->all(), [
            'account_name'   => 'required',
            'account_number' => 'required | numeric | unique:accounts',
            'user_id'        => 'numeric | required | exists:users,id'
        ]);

        if ($validationForAccount->fails()) {
            return $validationForAccount->errors();
        }

        // $user = User::where('id',$request->user_id)->first();
        // if($user){
            $account = Account::create([
                'account_name'      => $request->account_name,
                'account_number'    => $request->account_number,
                'user_id'           => $request->user_id
            ]);
    
            return response()->json([
                'status'            => true,
                'message'           => 'Account Created Successfully',
                'data'              => $account
            ], 200);
        // }
        // else{
        //     return "User Id Not Found";
        // }

        
    }

    public function list()
    {
        return Account::all();
    }
}
