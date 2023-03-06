<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AccountUser;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
    
        'type',
        'category',
        'account_user_id',
        'amount',
        'account_id'
    ];

    public function accounts()
    {
        return $this->belongsTo(Account::class);
    }

    public function accountUsers()
    {
        return $this->belongsTo(AccountUser::class,'account_user_id');
    }

    public function user()
    {
        return $this->hasManyThrough(User::class,Account::class,'user_id','id');
    }
    
}
