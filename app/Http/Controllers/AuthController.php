<?php

namespace App\Http\Controllers;
use App\Mail\ResetPassword;
use App\Mail\WelcomeMail;
use Illuminate\Support\Carbon;
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


class AuthController extends Controller
{
    public function create(Request $request)
    {
        //Validation
        $validateUser = Validator::make($request->all(), [
            'first_name'            => 'required|max:40|string',
            'last_name'             => 'required|max:40|string',
            'email'                 => 'required|max:40|email|unique:users,email',
            'phone'                 => 'required|regex:/[6-9][0-9]{9}/|unique:users,phone',
            'password'              => 'required|confirmed|min:8',
            'password_confirmation' => 'required'
        ]);

        //Validation Error
        if ($validateUser->fails()) {
            $errors = $validateUser->errors();
            return response()->json([
                'message'   => 'Validation Error',
                'error'     => $errors
            ]);
        }

        $user = User::create($request->only(['first_name', 'last_name', 'email', 'phone']) + [
            'password'                  => Hash::make($request->password),
            'email_verification_code'   => Str::random(40),
            'remember_token'            => Str::random(10)
        ]);

        //Default Account

        Account::create([
            'account_name'    => $user->first_name . " " . $user->last_name,
            'account_number'  => fake()->unique()->numerify('##########'),
            'is_default'      => true,
            'user_id'         => $user->id
        ]);

        //Welcome Mail
        Mail::to($user->email)->send(new WelcomeMail($user));
        //Response
        $apiToken = $user->createToken("API TOKEN")->plainTextToken;
        return response()->json([
            'status'    => true,
            'message'   => 'User Created Successfully',
            'token'     => $apiToken
        ], 200);
    }

    public function login(Request $request)
    {
        //Validation
        $validateUser   = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required',
            // 'dummy'     => 'sometimes|required',
        ]);

        //Validation Error
        if ($validateUser->fails()) {
            $errors = $validateUser->errors();
            return response()->json([
                'message'   => 'Validation Error',
                'error'     => $errors
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->is_onboarded == false) {
            return response()->json([
                'status'  => false,
                'message' => 'Email Not Verified!'
            ]);
        } else {
            // Checking user entered details
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                $apiToken = $user->createToken("API TOKEN")->plainTextToken;
                return response()->json([
                    'status'    => true,
                    'message'   => 'Login Successfully',
                    'token'     => $apiToken
                ], 200);
            } else {
                return response()->json([
                    'status'    => false,
                    'message'   => 'Password Incorrect'
                ]);
            }
        }
    }

    public function verifyEmail($verificaton_code)
    {
        $user = User::where('email_verification_code', $verificaton_code)->first();

        if ($user) {
            $user->update([
                'is_onboarded'      => true,
                'email_verified_at' => now()
            ]);
            return response()->json([
                'message'           => 'Verification Successful'
            ]);
        } else {
            return response()->json([
                'message'           => 'User not found'
            ]);
        }
    }

    public function forgotPasswordLink(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'email' => 'required|email|exists:users'
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

        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);
        PasswordReset::create($request->only(['email']) + [
            'token'         => $token,
            'created_at'    => now(),
            'expired_at'    => Carbon::now()->addDays(2)
        ]);
        $user['token'] = $token;
        if (Mail::to($user->email)->send(new ResetPassword($user))) {
            return response()->json([
                'status'  => true,
                'message' => 'Email Sent!',
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Some problem!',
            ]);
        }
    }

    public function forgotPassword(Request $request)
    {
        //Validation
        $validate = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:password_resets,email',
            'password'              => 'required|confirmed|min:8|max:40',
            'password_confirmation' => 'required',
            'token'                 => 'required|exists:password_resets,token'
        ]);

        //Validation Error
        if ($validate->fails()) {
            $errors = $validate->errors();
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'error'   => $errors
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $expdate = PasswordReset::where('email',$request->email)->first();
            if($expdate->expired_at > Carbon::now()){
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
                PasswordReset::where('email',$request->email)->delete();
                return response()->json([
                    'status'  => true,
                    'message' => 'Password Updated Successfully.',
                ]);
            }
            else{
                return response()->json([
                    'status'  => false,
                    'message' => 'Token Expired',
                ]);
            }
        }
        else {
            return response()->json([
                'status'  => false,
                'message' => 'User not found',
            ]);
        }
    }
    
}
