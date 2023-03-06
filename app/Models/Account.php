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

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userAccounts()
    {
        return $this->hasMany(AccountUser::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('created_at',"DESC");
    }

}
