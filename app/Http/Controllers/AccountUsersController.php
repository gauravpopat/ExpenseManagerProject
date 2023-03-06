<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AccountUser;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AccountUsersController extends Controller
{
    //Get list of Account_Users Table Records
    public function list()
    {
        $account = Account::where('user_id', Auth()->user()->id)->get();
        $accountUser = $account->load('accountUsers');
        if ($accountUser) {
            return response()->json([
                'message'            => 'Account Users',
                'account users'      => $accountUser,
            ]);
        } else {
            return response()->json([
                'message'   => 'No Account Users Data Found',
            ]);
        }
    }

    //Account_Users Insert
    public function insert(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'email'                 => 'required|email|max:40|exists:users,email',
            'account_id'            => 'required|numeric|digits:12|exists:accounts,id'
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
    public function update(Request $request)
    {
        $validationForAccount = Validator::make($request->all(), [
            'id'           => 'required|exists:account_users,id',
            'first_name'   => 'required|max:40|string',
            'last_name'    => 'required|max:40|string',
            'email'        => 'required|email|max:40|unique:account_users,email'
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
        $user = AccountUser::findOrFail($request->id);

        $user->update($request->only(['first_name', 'last_name', 'email']));
        return response()->json([
            'status'  => true,
            'message' => 'Data Updated Successfully',
        ]);
    }
    //Get Record from ID
    public function show($id)
    {
        $accountUser = AccountUser::findOrFail($id);
        $accountUser = $accountUser->load('account', 'transactions');
        return response()->json([
            'message'            => 'Account User Information',
            'account users'      => $accountUser
        ]);
    }

    // Account_Users Delete Record
    public function delete($id)
    {
        $user = AccountUser::findOrFail($id);
        $user->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Account User deleted successfully',
        ]);
    }
}
