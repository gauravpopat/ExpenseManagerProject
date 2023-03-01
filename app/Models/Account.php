<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
    
        'account_name',
        'account_number',
        'is_default',
        'user_id'
    ];

    public function account()
    {
        return $this->hasMany(Account::class);
    }
}
