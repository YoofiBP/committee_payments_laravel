<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PhpParser\Comment;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'is_verified',
        'email_verified_at',
        'is_admin'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean'
    ];

    protected $attributes = [
        'is_admin' => false,
        'total_contribution' => 0
    ];
    /**
     * @var mixed
     */

    public function setPasswordAttribute($value){
        $this->attributes['password'] = password_hash($value, PASSWORD_DEFAULT);
    }

    public function setPhoneNumberAttribute($value){
        $this->attributes['phone_number'] = trim(str_replace(' ', '', $value));
    }

    public function isAdministrator(){
        return $this->attributes["is_admin"];
    }

    public function contributions(){
        return $this->hasMany(Contribution::class);
    }
}
