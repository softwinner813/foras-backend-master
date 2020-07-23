<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'email_verify_token', 'password', 'api_token', 'role', 'first_name', 'last_name', 'company_name', 'logo', 'address', 'city', 'state', 'country', 'gender', 'phone', 'phone_verify_token', 'mobile', 'cv', 'commercial_registeration', 'sector', 'hourly_rate', 'marks', 'registered_by', 'latitude', 'longitude', 'experience', 'languages', 'skills', 'about_me', 'business_category', 'permission', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function jobs() {
        return $this->hasMany(Job::class, 'user_id', 'id');
    }

    public function favIndividuals() {
        return $this->hasMany(FavoriteIndividual::class, 'user_id', 'id');
    }

    public function favCorporates() {
        return $this->hasMany(FavoriteCorporate::class, 'user_id', 'id');
    }

    public function favJobs() {
        return $this->hasMany(FavoriteJob::class, 'user_id', 'id');
    }

    public function reviewsProvided() {
        return $this->hasMany(Review::class, 'provider_id', 'id');
    }

    public function reviewsReceived() {
        return $this->hasMany(Review::class, 'receiver_id', 'id');
    }
}
