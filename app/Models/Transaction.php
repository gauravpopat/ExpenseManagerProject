<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function accountUsers()
    {
        return $this->belongsToMany(AccountUser::class);
    }

    
}
