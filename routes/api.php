<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserAuthController;
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

Route::post('/login',[UserAuthController::class,'login']);

Route::group(['prefix' => 'auth'], function () {
    // Route::post('login', 'Auth\AuthController@login')->name('login');
    // Route::post('register', 'Auth\AuthController@register');
    // Route::group(['middleware' => 'auth:api'], function() {
    //     Route::get('logout', 'Auth\AuthController@logout');
    //     Route::get('user', 'Auth\AuthController@user');
    // });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


