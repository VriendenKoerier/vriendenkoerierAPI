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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('package/{id}', 'PacketController@show');
Route::get('packages/{pagesize}', 'PacketController@index');
Route::get('package/accept/{id}', 'PacketController@AcceptedUrl')->name('accept');
Route::get('package/deny/{id}', 'PacketController@DeniedUrl')->name('deny');

Route::group(['middleware' => 'auth:api'], function()
{
    Route::put('package', 'PacketController@store');
    Route::delete('package/{id}', 'PacketController@destroy');
    Route::post('package', 'PacketController@store');
    Route::get('packages/user/{pagesize}', 'PacketController@userPackets');
    Route::get('package/user/{id}', 'PacketController@userInfo');
    Route::patch('package/invite', 'PacketController@sendInvite');
});

//Authentication fix
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
      'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});
