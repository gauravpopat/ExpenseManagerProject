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
use App\Http\Traits\ResponseTrait;


class AuthController extends Controller
{
    use ResponseTrait;
    public function create(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'first_name'            => 'required|max:40|string',
            'last_name'             => 'required|max:40|string',
            'email'                 => 'required|max:40|email|unique:users,email',
            'phone'                 => 'required|regex:/[6-9][0-9]{9}/|unique:users,phone',
            'password'              => 'required|confirmed|min:8|max:40',
            'password_confirmation' => 'required'
        ]);

        $this->ValidationErrorsResponse($validation);

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
        $validation   = Validator::make($request->all(), [
            'email'     => 'required|email|exists:users,email',
            'password'  => 'required',
            // 'dummy'     => 'sometimes|required',
        ]);

        $this->ValidationErrorsResponse($validation);

        $user = User::where('email', $request->email)->first();

        if ($user->is_onboarded == false) {
            $this->returnResponse(false, "Email not verified...");
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
                $this->returnResponse(false, "Password Incorrect");
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
            $this->returnResponse(true, "Verification Successfull");
        } else {
            $this->returnResponse(false, "User not found");
        }
    }

    public function forgotPasswordLink(Request $request)
    {
        //Validation
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:users'
        ]);

        $this->ValidationErrorsResponse($validation);

        $user = User::where('email', $request->email)->first();

        $token = Str::random(64);

        PasswordReset::create($request->only(['email']) + [
            'token'         => $token,
            'created_at'    => now(),
            'expired_at'    => Carbon::now()->addDays(2)
        ]);

        $user['token'] = $token;
        
        if (Mail::to($user->email)->send(new ResetPassword($user))) {
            $this->returnResponse(true,"Email Sent!");
        } else {
            $this->returnResponse(true,"Email Not Sent!");
        }
    }

    public function forgotPassword(Request $request)
    {
        //Validation
        $validation = Validator::make($request->all(), [
            'email'                 => 'required|email|exists:password_resets,email',
            'password'              => 'required|confirmed|min:8|max:40',
            'password_confirmation' => 'required',
            'token'                 => 'required|exists:password_resets,token'
        ]);

        $this->ValidationErrorsResponse($validation);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $passwordReset = PasswordReset::where('email',$request->email)->first();
            if($passwordReset->expired_at > Carbon::now()){
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
                $passwordReset->delete();
                $this->returnResponse(true,"Password Updated Successfully");
            }
            else{
                $this->returnResponse(false,"Token Expired");
            }
        }
        else {
            $this->returnResponse(false,"User not found!");
        }
    }
    
}
