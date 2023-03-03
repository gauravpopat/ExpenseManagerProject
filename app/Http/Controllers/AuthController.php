<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function changePassword(Request $request)
    {
        //Validaton
        $validate = Validator::make($request->all(), [
            'old_password'          => 'required',
            'password'              => 'required|confirmed|min:8',
            'password_confirmation' => 'required'
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

        //Change Password

        $user = User::where('id', auth()->user()->id)->first();

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password'  => Hash::make($request->password)
            ]);
            return response()->json([
                'status'    => true,
                'message'   => 'Password Changed Successfully'
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Old Password Not Matched.'
            ]);
        }
    }
    public function userProfile()
    {
        $user = User::where('id',Auth::id())->first();
        if ($user) {
            $userProfile = $user->with('accounts', 'usersOfAccounts', 'transactions')->find(Auth::id());
            return response()->json([
                'message'           => 'User Profile',
                'User Data'         => $userProfile,
            ]);
        } else {
            return response()->json([
                'message'           => 'User Not Found',
            ]);
        }
    }

    public function accountDetails($id)
    {
        $account = Account::find($id);
        if ($account) {
            $accountDetails = Account::with('transactions')->find($id);
            return response()->json([
                'message'           => 'Account Details',
                'Account Data'      => $accountDetails
            ]);
        } else {
            return response()->json([
                'message'           => 'Account Not Found',
            ]);
        }
    }

    public function getAccountOfLoggedInUsers()
    {
        $userid = Auth::id();
        $account = User::with('accounts')->find($userid);
        return response()->json([
            'message'           => 'Accounts of Logged in Users',
            'accounts'          => $account
        ]);
    }
}
