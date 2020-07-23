<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('email_verify_token', 80)->unique()->nullable();
            $table->string('password');
            $table->string('api_token', 80)->unique()->nullable();
            $table->string('role')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('gender')->nullable();
            $table->string('phone')->unique()->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('phone_verify_token', 80)->unique()->nullable();
            $table->string('mobile')->nullable();
            $table->string('cv')->nullable();
            $table->string('commercial_registeration')->nullable();
            $table->string('sector')->nullable();
            $table->string('work_area')->nullable();
            $table->string('available_work_from_time')->nullable();
            $table->integer('hourly_rate')->nullable();
            $table->float('marks', 2, 1)->nullable();
            $table->string('registered_by')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('experience')->nullable();
            $table->string('languages')->nullable();
            $table->string('skills')->nullable();
            $table->text('about_me')->nullable();
            $table->string('business_category')->nullable();
            $table->string('permission')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
