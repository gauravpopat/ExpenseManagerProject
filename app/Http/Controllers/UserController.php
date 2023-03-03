<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function changePassword(Request $request)
    {
        //Validaton
        $validate = Validator::make($request->all(), [
            'old_password'          => 'required',
            'password'              => 'required|confirmed|min:8|max:40',
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
        $user->load('accounts','usersOfAccounts');
            return response()->json([
                'message'           => 'User Profile',
                'User Data'         => $user,
            ]);
    }
}
