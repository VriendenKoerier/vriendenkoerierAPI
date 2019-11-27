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

Route::get('packages', 'PackageController@index');
Route::get('package/{id}', 'PackageController@show');
Route::post('package', 'PackageController@store');
Route::put('package', 'PackageController@store');
Route::delete('package/{id}', 'PackageController@destroy');
