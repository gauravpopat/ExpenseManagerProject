<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AccountUser;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseTrait;

class AccountUsersController extends Controller
{
    use ResponseTrait;
    //Get list of Account_Users Table Records
    public function list()
    {
        $account = Account::where('user_id',auth()->user()->id)->first();
        $accountUser = AccountUser::where('account_id', $account->id)->first()->load('transactions','accounts');
        if ($accountUser) {
            return $this->returnResponse(true, "Account Users Information",$accountUser);
        } else {
            return $this->returnResponse(false, "No Account Users Data Found");
        }
    }

    //Account_Users Insert
    public function insert(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'                 => 'required|email|max:40|exists:users,email',
            'account_id'            => 'required|numeric|digits:12|exists:accounts,id'
        ]);

        if($validation->fails())
            return $this->ValidationErrorsResponse($validation);

        $user = User::where('email', $request->email)->first();

        AccountUser::create($request->only(['email', 'account_id']) + [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name
        ]);

        return $this->returnResponse(true, "Account User Inserted Successfully...");
    }

    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'           => 'required|exists:account_users,id',
            'first_name'   => 'required|max:40|string',
            'last_name'    => 'required|max:40|string',
            'email'        => 'required|email|max:40|unique:account_users,email'
        ]);
        if($validation->fails())
            return $this->ValidationErrorsResponse($validation);

        $user = AccountUser::findOrFail($request->id);

        $user->update($request->only(['first_name', 'last_name', 'email']));
        
        return $this->returnResponse(true, "Record Updated Successfully...");

    }
    
    public function show($id)
    {
        $accountUser = AccountUser::findOrFail($id);
        return $this->returnResponse(true, "Account User Information",$accountUser);
    }

    public function delete($id)
    {
        AccountUser::findOrFail($id)->delete();
        return $this->returnResponse(true, "Account User Deleted Successfully...");
    }
}
