<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountUser;
use Illuminate\Support\Facades\Validator;


class AccountUsersController extends Controller
{
    //Account_Users Insert
    public function insert(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required | email | unique:users',
            'account_id'            => 'numeric | required | exists:accounts,id'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return $validate->errors();
        }

        //Insert
        $account_users = AccountUser::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'account_id'        => $request->account_id
        ]);

        return response()->json([
            'status'            => true,
            'message'           => 'Inserted Successfully',
            'data'              => $account_users
        ], 200);
    }

    // Account_Users Update
    public function update($id, Request $request)
    {
        $account_users = AccountUser::find($id);

        // If request is null then it will store the old value in update otherwise new value(req value)

        if ($request->first_name == null) {
            $first_name = $account_users->first_name;
        } else {
            $first_name = $request->first_name;
        }

        if ($request->last_name == null) {
            $last_name = $account_users->last_name;
        } else {
            $last_name = $request->last_name;
        }

        if ($request->email == null) {
            $email = $account_users->email;
        } else {
            $email = $request->email;
        }

        $account_users->update([
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'email'        => $email
        ]);
        return $account_users;
    }

    // Account_Users Delete Record
    public function delete($id)
    {
        $account_users = AccountUser::find($id);
        $account_users->delete();
        return "Account Deleted Successfully";
    }

    //Get list of Account_Users Table Records
    public function list()
    {
        return AccountUser::all();
    }

    //Get Record from ID
    public function show($id)
    {
        return AccountUser::find($id);
    }
}

