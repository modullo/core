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
    Route::group(['namespace' => 'Authentication','prefix' => 'auth'], static function(){
      Route::post('setup','AuthController@setup');
      Route::post('register',['middleware' => ['client'],'uses' => 'AuthController@register']);
          Route::group(['middleware' => ['auth']],static function(){
              Route::get('me','AuthController@getUser');
        });
    });


  Route::group(['middleware' => ['auth']], function () {
    Route::group(['middleware' => ['role:overlord']], function () {
      Route::get('/test-overlord-rights',function(){
        return 'hello i am overlord';
      });
    });

    Route::group(['middleware' => ['role:administrative|overlord']], function () {
      Route::get('/test-admin-rights',function(){
        return 'hello i am overlord or administrative';
      });
    });
  });

});


