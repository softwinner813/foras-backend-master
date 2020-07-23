<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteIndividual extends Model
{
    //
    protected $fillable = [
        'user_id', 'individual_id',
    ];

    public function individuals() {
        return $this->hasOne(User::class, 'id', 'individual_id');
    }
}
