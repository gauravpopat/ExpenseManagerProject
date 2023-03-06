<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
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
            return $this->ValidationErrorsResponse($validation);

        //Change Password

        $user = User::where('id', auth()->user()->id)->first();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password'  => Hash::make($request->password)
            ]);
            return $this->returnResponse(true,"Password Changed Successfully");
        } else {
            return $this->returnResponse(false,"Old Password Not Matched.");
        }
    }
    
    public function userProfile()
    {
        $user = User::where('id',Auth::id())->first()->load('accounts','accountUsers','transactions');
        return $this->returnResponse(true,"User Profile",$user);
    }
}
