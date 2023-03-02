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
            'first_name'            => 'required | max:40',
            'last_name'             => 'required | max:40',
            'email'                 => 'required | email | unique:users',
            'account_id'            => 'numeric | required | exists:accounts,id'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $validate->errors()
            ]);
        }
        $account_users = AccountUser::create($request->only(['first_name','last_name','email','account_id']));

        return response()->json([
            'status'            => true,
            'message'           => 'Inserted Successfully',
            'data'              => $account_users
        ], 200);
    }

    // Account_Users Update
    public function update($id, Request $request)
    {
        $account_users = AccountUser::findOrFail($id);
        $validationForAccount = Validator::make($request->all(), [
            'first_name'   => 'required | max:40',
            'last_name'    => 'required | max:40',
            'email'        => 'required | email | unique:account_users,email'
        ]);

        //Validation Error
        if ($validationForAccount->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $validationForAccount->errors()
            ]);
        }

        $account_users->update($request->only(['first_name','last_name','email']));

        return response()->json([
            'status'  => true,
            'message' => 'Data Updated Successfully',
        ]);
    }

    // Account_Users Delete Record
    public function delete($id)
    {
        $account_users = AccountUser::findOrFail($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Data deleted successfully',
        ]);
    }

    //Get list of Account_Users Table Records
    public function list()
    {
        return response()->json([
            'message'   => 'Data',
            'data'      => AccountUser::all()
        ]);
    }

    //Get Record from ID
    public function show($id)
    {
        return response()->json([
            'message'   => 'Data',
            'data'      => AccountUser::findOrFail($id)
        ]);
    }
}

