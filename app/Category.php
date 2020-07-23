<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = [
        'category_name',
    ];

    public function jobs() {
        return $this->hasOne(Job::class, 'id', 'category_id');
    }
}
