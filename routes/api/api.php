<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['as' => 'auth.'], function () {

  Route::post('login', 'AuthController@login');
  Route::post('login_social', 'AuthController@loginSocial');
  Route::post('/register', 'AuthController@register');
  Route::middleware('auth:api')->get('/me', 'AuthController@me');
  Route::middleware('auth:api')->patch('/me', 'AuthController@updateProfile');

  Route::post('/resend_confirmation_email/{user}', 'AuthController@resendConfirmationEmail');
  Route::get('/resend_confirmation_email/{user}', 'AuthController@resendConfirmationEmail');

  Route::post('/verify_otp', 'AuthController@verify_otp');

  Route::post('/reset_password_mobile', 'AuthController@reset_password_mobile');
  Route::post('/reset_password_email', 'AuthController@reset_password_email');

});