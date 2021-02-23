<?php


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;
Route::get('', function(){
  return 'Welcome to modullo api core';
});
Route::group(['prefix' => 'v1'], static function(){

    Route::get('', function(){
      return 'Welcome to modullo api core';
    });

    Route::group(['middleware' => 'client'], static function(){
      Route::group(['namespace' => 'Authentication','prefix' => 'auth'], static function(){
        Route::post('register','AuthController@register');
      });
    });
});


