<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        // 'account_name',
        // 'account_number',
        'role',
        'email_verification_code',
        'is_onboarded',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_onboarded'      => 'boolean'
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function accountUsers()
    {
        return $this->hasManyThrough(AccountUser::class,Account::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class,Account::class)->orderBy('created_at', 'DESC');
    }
}
