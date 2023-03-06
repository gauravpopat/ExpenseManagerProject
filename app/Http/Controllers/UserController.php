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
use App\Http\Traits\ResponseTrait;

class UserController extends Controller
{
    use ResponseTrait;
    public function changePassword(Request $request)
    {
        //Validaton
        $validation = Validator::make($request->all(), [
            'old_password'          => 'required',
            'password'              => 'required|confirmed|min:8|max:40',
            'password_confirmation' => 'required'
        ]);
        
        if($validation->fails())
            $this->ValidationErrorsResponse($validation);

        //Change Password

        $user = User::where('id', auth()->user()->id)->first();

        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password'  => Hash::make($request->password)
            ]);
            $this->returnResponse(true,"Password Changed Successfully");
        } else {
            $this->returnResponse(false,"Old Password Not Matched.");
        }
    }
    
    public function userProfile()
    {
        $user = User::where('id',Auth::id())->first();
        $user->load('accounts','usersOfAccounts');
        return $this->returnResponse(true,"User Profile",$user);
    }
}
