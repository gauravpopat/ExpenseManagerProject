<?php

namespace App\Http\Controllers\Auth;

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
            'password'              => 'required|confirmed |min:8',
            'password_confirmation' => 'required'
        ]);
        //Validation Error
        if ($validate->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $validate->errors()
            ]);
        }

        //Change Password
        $user = User::where('id', Auth::user()->id)->first();
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
    public function userProfile($id)
    {
        $user  = User::with('account','userOfAccount','transactions')->findOrFail($id);
        return response()->json([
            'message'           => 'User Profile',
            'User Data'         => $user,
        ]);
    }

    public function accountDetails($id)
    {
        $account = Account::with('transaction')->findOrFail($id);
        return response()->json([
            'message'           => 'Account Details',
            'Account Data'      => $account
        ]);
    }
}
