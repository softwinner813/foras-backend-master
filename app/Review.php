<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    //
    protected $fillable= [
        'job_id', 'provider_id', 'receiver_id', 'marks', 'comment', 'is_published',
    ];

    public function provider() {
        return $this->belongsTo(User::class, 'provider_id', 'id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}
