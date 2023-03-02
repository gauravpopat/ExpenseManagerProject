<?php

namespace App\Http\Controllers\Auth;

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
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request)
    {

        //Validation
        $validateUser = Validator::make($request->all(), [
            'first_name'    => 'required | max:40',
            'last_name'     => 'required | max:40',
            'email'         => 'required | max:40 | email | unique:users,email',
            'phone'         => 'required|regex:/[6-9][0-9]{9}/ | unique:users,phone',
            'password'      => 'required | confirmed |min:8',
        ]);

        //Validation Error
        if ($validateUser->fails()) {
            return response()->json([
                'message'   => 'Validation Error',
                'error'     => $validateUser->errors()
            ]);
        }
        //Create User
        $request['password']                    = Hash::make($request->password);
        $request['email_verification_code']     = Str::random(40);
        $request['remember_token']              = Str::random(10);

        $user = User::create($request->only(['first_name', 'last_name', 'email', 'phone', 'password', 'email_verification_code', 'remember_token']));

        //Default Account
        $request['account_name']    = $user->first_name . " " . $user->last_name;
        $request['account_number']  = fake()->unique()->numerify('##########');
        $request['is_default']      = true;
        $request['user_id']         = $user->id;
        Account::create($request->only(['account_name', 'account_number', 'is_default', 'user_id']));

        //Welcome Mail
        Mail::to($user->email)->send(new WelcomeMail($user));
        //Response
        return response()->json([
            'status'    => true,
            'message'   => 'User Created Successfully',
            'token'     => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }

    public function login(Request $request)
    {
        //Validation
        $validateUser   = Validator::make($request->all(), [
            'email'     => 'required | email | max:40',
            'password'  => 'required | min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->is_onboarded == false) {
            return response()->json([
                'status'  => false,
                'message' => 'Email Not Verified!'
            ]);
        } else {
            // Checking user entered details
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json([
                    'status'    => true,
                    'message'   => 'Login Successfully',
                    'token'     => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            }
            // If wrong details
            return response()->json([
                'status'    => false,
                'message'   => 'Login Failed! Try again...'
            ], 200);
        }
    }

    public function verify($verificaton_code)
    {
        $user = User::where('email_verification_code', $verificaton_code)->first();
        $user->update([
            'is_onboarded'      => true,
            'email_verified_at' => now()
        ]);
        return response()->json([
            'message'           => 'Verification Successfull'
        ]);
    }



    public function forgotPassword(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'email' => 'required | email'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return response()->json([
                'status'    => false,
                'message'   => 'Validation Error',
                'error'     => $validate->errors()
            ]);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            Mail::to($user->email)->send(new ResetPassword($user));
            return response()->json([
                'status'  => true,
                'message' => 'Email Sent! Check it',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Email not in the database.',
            ]);
        }
    }

    public function forgotPw(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'password'              => 'required | confirmed |min:8',
            'password_confirmation' => 'required',
            'token'                 => 'required'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'error'   => $validate->errors()
            ]);
        }

        $user = User::where('email_verification_code', $request->token)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'status'  => true,
                'message' => 'Password Updated Successfully.',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Token Not Valid',
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        //Validaton
        $validate = Validator::make($request->all(), [
            'old_password'          => 'required',
            'password'              => 'required | confirmed |min:8',
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
        $user = User::findOrFail($id);
        $account = User::findOrFail($id)->account;

        return response()->json([
            'message'       => 'User Detail',
            'User'          => $user,
            'User Account'  => $account
        ]);
    }
}
