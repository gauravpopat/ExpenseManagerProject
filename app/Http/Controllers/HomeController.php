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
use Illuminate\Support\Str;

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
            'email_verification_code' =>  Str::random(40),
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
        //Mail::to($user->email)->send(new WelcomeMail($user));

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

    public function verify($verificaton_code)
    {

        $user = User::where('email_verification_code', $verificaton_code)->first();
        $user->update([
            'is_onboarded' => true
        ]);
        return "Verification Successfull";
    }


    public function update($id, Request $request)
    {
        $account = Account::find($id);

        // If request is null then it will store the old value in update otherwise new value(req value)

        if ($request->account_name == null) {
            $account_name = $account->account_name;
        } else {
            $account_name = $request->account_name;
        }

        if ($request->account_number == null) {
            $account_number = $account->account_number;
        } else {
            $account_number = $request->account_number;
        }

        $account->update([
            'account_name'   => $account_name,
            'account_number' => $account_number,
        ]);
        return $account;
    }

    public function delete($id)
    {
        $account = Account::find($id);

        if ($account->is_default == true) {
            return "Deletation not allowed for default account";
        } else {
            $account->delete();
            return "Account Deleted Successfully";
        }
    }

    public function show($id)
    {
        return Account::find($id);
    }

    public function insert(Request $request)
    {
        $validationForAccount = Validator::make($request->all(), [
            'account_name'   => 'required',
            'account_number' => 'required | numeric | unique:accounts'
        ]);

        if ($validationForAccount->fails()) {
            return $validationForAccount->errors();
        }

        $account = Account::create([
            'account_name'      => $request->account_name,
            'account_number'    => $request->account_number,
            'user_id'           => 4
        ]);

        return response()->json([
            'status'            => true,
            'message'           => 'Account Created Successfully',
            'data'              => $account
        ], 200);
    }

    public function list()
    {
        return Account::all();
    }
}
