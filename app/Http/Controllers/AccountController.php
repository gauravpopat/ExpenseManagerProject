<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ResponseTrait;

class AccountController extends Controller
{
    use ResponseTrait;
    public function list()
    {
        $accounts = Account::where('user_id',auth()->user()->id)->first()->load('accountUsers','transactions');
        return $this->returnResponse(true,"Accounts",$accounts);
    }

    public function insert(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'account_name'   => 'required|max:40|string',
            'account_number' => 'required|numeric|digits:12|unique:accounts,account_number',
            'user_id'        => 'numeric|required|exists:users,id'
        ]);

        if ($validation->fails())
            return $this->validationErrorsResponse($validation);

        Account::create($request->only(['account_name', 'account_number', 'user_id']));

        return $this->returnResponse(true, "Account Created Successfully");
    }

    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id'             => 'required|exists:accounts,id',
            'account_name'   => 'required|max:40|string',
            'account_number' => 'required|numeric|digits:12|unique:accounts,account_number,'.$request->id
        ]);

        if($validation->fails())
            return $this->validationErrorsResponse($validation);

        Account::findOrFail($request->id)->update($request->only(['account_name', 'account_number']));

        return $this->returnResponse(true, "Record Updated Successfully");
    }

    public function show($id)
    {
        $account = Account::findOrFail($id);
        return $this->returnResponse(true, "Account", $account);
    }

    public function delete($id)
    {
        $account = Account::findOrFail($id);
        if ($account->is_default == true) {
            return $this->returnResponse(false, "Default account can not be delete!");
        } else {
            $account->delete();
            return $this->returnResponse(true, "Account Deleted Successfully...");
        }
    }
}
