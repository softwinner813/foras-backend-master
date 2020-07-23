<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteJob extends Model
{
    //
    protected $fillable = [
        'user_id', 'job_id',
    ];

    public function jobs() {
        return $this->hasOne(Job::class, 'id', 'job_id');
    }

    public function users() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}