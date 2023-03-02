<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            // 'first_name'            => 'required|max:40',
            // 'last_name'             => 'required|max:40',
            'email'                 => 'required|email|exists:users,email',
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
        $getUser = User::where('email',$request->email)->first();
        $fn = $getUser->first_name;
        $ln = $getUser->last_name;

        $account_users = AccountUser::create($request->only(['email','account_id'])+[
            'first_name' => $fn,
            'last_name'  => $ln
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
        $validationForAccount = Validator::make($request->all(), [
            'first_name'   => 'required|max:40',
            'last_name'    => 'required|max:40',
            'email'        => 'required|email|unique:account_users,email'
        ]);

        //Validation Error
        if ($validationForAccount->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $validationForAccount->errors()
            ]);
        }
        AccountUser::findOrFail($id)->update($request->only(['first_name','last_name','email']));

        return response()->json([
            'status'  => true,
            'message' => 'Data Updated Successfully',
        ]);
    }

    // Account_Users Delete Record
    public function delete($id)
    {
        AccountUser::findOrFail($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Data deleted successfully',
        ]);
    }

    //Get list of Account_Users Table Records
    public function list()
    {
        $data = AccountUser::all();
        return response()->json([
            'message'   => 'Data',
            'data'      => $data
        ]);
    }

    //Get Record from ID
    public function show($id)
    {
        $data = AccountUser::findOrFail($id);
        return response()->json([
            'message'   => 'Data',
            'data'      => $data
        ]);
    }
}

