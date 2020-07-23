<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
    protected $fillable= [
        'type', 'message', 'created_by', 
    ];
}
