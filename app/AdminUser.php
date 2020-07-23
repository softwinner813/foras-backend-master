<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    //
    protected $fillable= [
        'name', 'email', 'password', 'api_token', 'role', 'first_name', 'last_name', 'phone', 'logo', 'permission', 
    ];

    protected $hidden = [
        'password', 
    ];
}
