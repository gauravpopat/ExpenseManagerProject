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
            'first_name'            => 'required',
            'last_name'             => 'required',
            'email'                 => 'required | email | unique:users',
            'phone'                 => 'required|regex:/[6-9][0-9]{9}/ | unique:users',
            'password'              => 'required | confirmed |min:8',
        ]);

        //Validation Error
        if ($validateUser->fails()) {
            return $validateUser->errors();
        }

        //Create User
        $user = User::create([
            'first_name'        => $request->first_name,
            'last_name'         => $request->last_name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'password'          => Hash::make($request->password),
            'email_verification_code' =>  Str::random(40),
            'remember_token' => Str::random(10),
            // 'account_name'      => $request->first_name . " " . $request->last_name,
            // 'account_number'    => fake()->unique()->numerify('##########'),
        ]);

        //Default Account
        $account = Account::create([
            'account_name'      => $user->first_name . " " . $user->last_name,
            'account_number'    => fake()->unique()->numerify('##########'),
            'is_default'        => true,
            'user_id'           => $user->id
        ]);

        //Welcome Mail
        Mail::to($user->email)->send(new WelcomeMail($user));

        //Response
        return response()->json([
            'status'            => true,
            'message'           => 'User Created Successfully',
            'token'             => $user->createToken("API TOKEN")->plainTextToken,
            'user_data'         => User::find($user)
        ], 200);
    }


    public function login(Request $request)
    {
        //Validation
        $validateUser = Validator::make($request->all(), [
            'email'                 => 'required | email',
            'password'              => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user->is_onboarded == false) {
            return "Verification Problem!!";
        } else {
            // Checking user entered details
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = User::where('email', $request->email)->first();
                return response()->json([
                    'status'            => true,
                    'message'           => 'Login Successfully',
                    'token'             => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            }

            // If wrong details
            return response()->json([
                'status'            => false,
                'message'           => 'Login Failed! Try again...'
            ], 200);
        }
    }



    public function verify($verificaton_code)
    {

        $user = User::where('email_verification_code', $verificaton_code)->first();
        $user->update([
            'is_onboarded' => true,
            'email_verified_at' => now()
        ]);
        return "Verification Successfull";
    }


    

    public function forgotPassword(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'email'  => 'required | email'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return $validate->errors();
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            Mail::to($user->email)->send(new ResetPassword($user));
            return "Mail Sent! Check it";
        } else {
            return "This email not in the database";
        }
    }

    public function forgotPw(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'password' => 'required | confirmed |min:8',
            'password_confirmation' => 'required',
            'token' => 'required'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return $validate->errors();
        }

        $user = User::where('email_verification_code', $request->token)->first();
        if ($user) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            return $user->email;
        } else {
            return "Token Not Valid";
        }
    }

    public function changePassword(Request $request)
    {
        //Validaton
        $validate = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required | confirmed |min:8',
            'password_confirmation' => 'required'
        ]);

        //Validation Error
        if ($validate->fails()) {
            return $validate->errors();
        }


        $user = User::where('id', Auth::user()->id)->first();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            return "Dear user " . Auth::user()->first_name . ", Password Changed Successfully";
        } else {
            return "Old Password Not Matched";
        }
    }


    public function userProfile($id)
    {
        $user = User::find($id);
        $account = User::find($id)->account;
        return response()->json([
            'message'           => 'User Detail',
            'User'              => $user,
            'User Account'      => $account
        ]);
    }
}