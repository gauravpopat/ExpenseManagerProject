<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
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
            // 'account_name'      => $request->first_name . " " . $request->last_name,
            // 'account_number'    => fake()->unique()->numerify('##########'),
        ]);

        //Default Account
        $account = Account::create([
            'email'             => $user->email,
            'account_name'      => $user->first_name." ".$user->last_name,
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

        // Checking user entered details
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'status'            => true,
                'message'           => 'Login Successfully'
            ], 200);
        }

        //If wrong details
        return response()->json([
            'status'            => false,
            'message'           => 'Login Failed! Try again...'
        ], 200);
    }

    public function verify()
    {
        
    }
}
