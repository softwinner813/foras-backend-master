<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'AuthController@login');
    Route::post('login/socials', 'AuthController@loginBySocial');
    Route::post('logout', 'AuthController@logout');
    Route::post('register', 'AuthController@register');
    Route::post('verify/email', 'AuthController@verifyEmail');
    Route::post('verify/phone', 'AuthController@verifyPhone');
    Route::get('verify/{type}/{token}/{user_id}/{expire_at}', 'AuthController@verifyUser');
    Route::post('register/profile', 'AuthController@registerProfile');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'categories'], function() {
    Route::get('/', 'CategoryController@index');
    Route::post('/create', 'CategoryController@create');
});

Route::group(['prefix' => 'jobs'], function() {
    Route::post('create', 'JobController@create');
    Route::post('read', 'JobController@index');
    Route::put('update/{id}', 'JobController@update');
    Route::delete('delete/{id}', 'JobController@delete');
    Route::get('details/{id}', 'JobController@details');
});

Route::group(['prefix' => 'users'], function() {
    Route::post('/individuals', 'UserController@individuals');
    Route::get('/individuals/{id}', 'UserController@details');
    Route::post('/corporates', 'UserController@corporates');
    Route::get('/corporates/{id}', 'UserController@details');
    Route::post('/profile/update', 'UserController@profileUpdate');
    Route::post('/profile/upload', 'UserController@profileUpload');
});

Route::group(['prefix' => 'favorites'], function() {
    // Route::get('/', 'FavoriteController@index');
    Route::get('/individuals', 'FavoriteController@favIndividuals');
    Route::post('/individuals/toggle', 'FavoriteController@toggleFavIndividual');
    Route::get('/corporates', 'FavoriteController@favCorporates');
    Route::post('/corporates/toggle', 'FavoriteController@toggleFavCorporate');
    Route::get('/jobs', 'FavoriteController@favJobs');
    Route::post('/jobs/toggle', 'FavoriteController@toggleFavJob');
    // Route::post('create', 'FavoriteController@create');
    // Route::delete('delete/{id}', 'FavoriteController@delete');
});

Route::group(['prefix' => 'reviews'], function() {
    Route::get('/', 'ReviewController@index');
    Route::post('/create', 'ReviewController@create');
});

Route::group(['prefix' => 'messages'], function() {
    Route::get('/', 'MessageController@index');
    Route::post('/create', 'MessageController@create');
    Route::post('/delete/{id}', 'MessageController@delete');
});

Route::group(['prefix' => 'notifications'], function() {
    Route::get('/', 'NotificationController@index');
    Route::post('/create', 'NotificationController@create');
});







// ============= ********** ============== //
// ============= Atisan CMD ============== //
// ============= ********** ============== //
Route::get('/cache-clear', function() {
    $exitCode = Artisan::call('cache:clear');
    // return what you want
    return response()->json(['result' => $exitCode], 200);
});

Route::get('/config-clear', function() {
    $exitCode = Artisan::call('config:clear');
    // return what you want
    return response()->json(['result' => $exitCode], 200);
});

Route::get('/storage-link', function() {
    $exitCode = Artisan::call('storage:link');
    // return what you want
    return response()->json(['result' => $exitCode], 200);
});


// ============== ************ ============== //
// ============== Only Web API ============== //
// ============== ************ ============== //


Route::group(['prefix' => 'web'], function () {
    Route::post('/register', 'AuthController@registerFromWeb');
    Route::post('/login/socials', 'AuthController@loginBySocialFromWeb');
    Route::post('/submitTicket', 'TicketController@create');
});

// ============= Admin Panel ============== //
Route::group(['prefix' => 'admin'], function () {
    Route::post('/login', 'AdminController@login');
    Route::post('/individuals', 'AdminController@getIndividuals');
    Route::post('/corporates', 'AdminController@getCorporates');
    Route::post('/updateUserPemission', 'AdminController@updateUserPemission');
    Route::post('/jobs', 'AdminController@getJobs');
    Route::post('/updateJobStatus', 'AdminController@updateJobStatus');
    Route::post('/notifications', 'AdminController@getNotifications');
    Route::post('/newNotification', 'AdminController@newNotification');
    Route::post('/messages', 'AdminController@getMessages');
    Route::post('/tickets', 'AdminController@getTickets');
});