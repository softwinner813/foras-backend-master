<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    //
    protected $fillable = [
        'user_id', 'job_name', 'category_id', 'location', 'start_date', 'end_date', 'workdays', 'workhours', 'salary_type', 'salary_amount', 'salary_rate', 'languages', 'job_details', 'status',
    ];

    public function users() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function categories() {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function favJobs() {
        return $this->belongsTo(FavoriteJob::class, 'job_id', 'id');
    }
}
