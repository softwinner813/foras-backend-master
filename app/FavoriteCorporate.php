<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteCorporate extends Model
{
    //
    protected $fillable = [
        'user_id', 'corporate_id',
    ];

    public function corporates() {
        return $this->hasOne(User::class, 'id', 'corporate_id');
    }
}
