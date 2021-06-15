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

Route::get('', function () {
    return 'kindly add version v1 to access the modullo api core';
});


Route::group(['prefix' => 'v1'], static function () {
    Route::get('', function () {
        return 'Welcome to modullo api core';
    });

    //demo routes to test the roles and permission system
    Route::group(['middleware' => ['auth']], function () {
        Route::group(['middleware' => ['role:overlord']], function () {
            Route::get('/test-overlord-rights', function () {
                return 'hello i am overlord';
            });
        });

        Route::group(['middleware' => ['role:tenant']], function () {
            Route::get('/test-tenants-rights', function () {
                return 'hello i am Tenant';
            });
        });

        Route::group(['middleware' => ['role:administrative|overlord']], function () {
            Route::get('/test-admin-rights', function () {
                return 'hello i am overlord or administrative';
            });
        });
    });


    Route::group(['namespace' => 'Lms','prefix' => 'lms'],function(){
        Route::group(['namespace' => 'Authentication', 'prefix' => 'auth'], static function () {
            Route::post('setup/{provider}', 'AuthController@setup');
            Route::group(['middleware' => ['lms_user','auth:api']], static function () {
                Route::get('me', 'AuthController@getUser');
            });

        });

        Route::group(['middleware' => ['lms_user','auth:api']], static function () {
            Route::group(['prefix' => 'tenants'],function(){
                Route::post('','TenantsController@create');
                Route::put('{tenantId}','TenantsController@update');
                Route::get('{tenantId}','TenantsController@single');
            });

        });


        Route::group(['middleware' => ['lms_user','auth:api']], static function () {

            Route::group(['prefix' => 'programs'],function(){
                Route::get('','ProgramsController@index');
                Route::post('','ProgramsController@create');
                Route::put('{programId}','ProgramsController@update');
                Route::get('{programId}','ProgramsController@single');
            });

            Route::group(['prefix' => 'courses'],function(){
                Route::get('all','CoursesController@all');
                Route::get('all/{programId}','CoursesController@index');
                Route::post('create/{programId}','CoursesController@create');
                Route::put('{courseId}','CoursesController@update');
                Route::get('{courseId}','CoursesController@single');
            });


            Route::group(['prefix' => 'modules'],function(){
                Route::get('all/{courseId}','ModulesController@index');
                Route::get('','ModulesController@all');
                Route::post('create/{courseId}','ModulesController@create');
                Route::put('{moduleId}','ModulesController@update');
                Route::get('{moduleId}','ModulesController@single');
            });


            Route::group(['prefix' => 'lessons'],function(){
                Route::get('all/{moduleId}','LessonsController@index');
                Route::get('','LessonsController@all');
                Route::post('create/{moduleId}','LessonsController@create');
                Route::put('{lessonId}','LessonsController@update');
                Route::get('{lessonId}','LessonsController@single');
            });

            Route::group(['namespace' => 'Resource'],function(){
                Route::group(['prefix' => 'assets'],function(){
                    Route::get('','AssetsController@all');
                    Route::post('','AssetsController@create');
                    Route::get('{assetId}','AssetsController@single');
                    Route::put('{assetId}','AssetsController@update');
                    Route::post('custom/upload','AssetsController@customUpload');
                });

                Route::group(['prefix' => 'quiz'],function(){
                    Route::get('','QuizController@index');
                    Route::post('','QuizController@create');
                    Route::put('{quizId}','QuizController@update');
                    Route::get('{quizId}','QuizController@single');

                    Route::group(['prefix' => 'questions'],function(){
                        Route::post('add/{quizId}','QuizController@addQuestion');
                        Route::put('{questionId}','QuizController@updateQuestion');
                        Route::delete('{questionId}','QuizController@deleteQuestion');
                    });
                });


            });

        });




    });

});


