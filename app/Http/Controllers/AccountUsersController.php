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
            'email'                 => 'required|email|exists:users,email',
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

        //no need to add if($user) because of validation
        $user = User::where('email', $request->email)->first();

        $account_users = AccountUser::create($request->only(['email', 'account_id']) + [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name
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
            $errors = $validationForAccount->errors();
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $errors
            ]);
        }
        $user = AccountUser::find($id);
        if ($user) {
            $user->update($request->only(['first_name', 'last_name', 'email']));
            return response()->json([
                'status'  => true,
                'message' => 'Data Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Account User not found',
            ]);
        }
    }

    // Account_Users Delete Record
    public function delete($id)
    {
        $user = AccountUser::find($id);
        if ($user) {
            AccountUser::find($id)->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Account User deleted successfully',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'User not found'
            ]);
        }
    }

    //Get list of Account_Users Table Records
    public function list()
    {
        $accountUsers = AccountUser::all();
        return response()->json([
            'message'   => 'Account Users Data',
            'data'      => $accountUsers
        ]);
    }

    //Get Record from ID
    public function show($id)
    {
        $accountUser = AccountUser::find($id);
        if ($accountUser) {
            return response()->json([
                'message'   => 'Account User',
                'data'      => $accountUser
            ]);
        } else {
            return response()->json([
                'message'   => 'Account user not found'
            ]);
        }
    }
}
