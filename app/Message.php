<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    public $fillable = [
        'sender', 'receiver', 'type', 'subject', 'message', 'read_at', 
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
