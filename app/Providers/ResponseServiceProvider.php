<?php

namespace App\Providers;

use App\support\Responses\Codes;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\ResponseFactory;

class ResponseServiceProvider extends ServiceProvider
{

  public function boot(ResponseFactory $factory): void
  {
    $factory->macro('created', function ($message = '', $data = null, $key = null) use ($factory) {
      $format = [
        'data' => [
          'status' => 201,
          'code' => Codes::CREATED_SUCCESS,
          'title' => $message,
          $key =>  $data
        ]
      ];
      return $factory->make($format,201);
    });

    $factory->macro('updated', function (string $message = '', $data = null, $key=null) use ($factory){
      $format = [
        'data' => [
          'status' => 202,
          'code' => Codes::UPDATED_SUCCESS,
          'title' => $message,
          $key =>  $data
        ]
      ];

      return $factory->make($format,201);
    });

    $factory->macro('fetch', function (string $message = '', $data = null, $key =  null) use ($factory){
      $format = [
        'data' => [
          'status' => 200,
          'code' => Codes::FETCHED_SUCCESS,
          'title' => $message,
          $key =>  $data
        ]
      ];

      return $factory->make($format,200);
    });

    $factory->macro('deleted', function (string $message = '') use ($factory){
      $format = [
        'data' => [
          'status' => 204,
          'code' => Codes::UPDATED_SUCCESS,
          'title' => $message,
        ]
      ];

      return $factory->make($format,204);
    });
  }

  public function register(){

  }

}