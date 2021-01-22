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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'AuthController@login');
Route::post('login_social', 'AuthController@loginSocial');
Route::post('/register', 'AuthController@register');

Route::post('/resend_confirmation_email/{user}', 'AuthController@resendConfirmationEmail');
Route::get('/resend_confirmation_email/{user}', 'AuthController@resendConfirmationEmail');


Route::post('/reset_password_email', 'AuthController@reset_password_email');