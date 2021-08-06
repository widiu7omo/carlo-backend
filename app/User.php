<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    //protected $rememberTokenName = true;

    protected $fillable = [
        'userid','name','email','password','remember_token','device_id','ip','country','refby','updated_at'
    ];
    
    protected $hidden = [
        'password','remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
