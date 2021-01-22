<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/login', function () {
    return redirect(env('DRLINK_HOST'));
})->name('login');
  
Route::get('/email/verify/{id}', 'Api\AuthController@verify')->middleware(['signed'])->name('verification.verify');
