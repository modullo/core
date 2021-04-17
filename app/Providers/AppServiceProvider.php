<?php

namespace App\Providers;

use Hashids\Hashids;
use Illuminate\Support\ServiceProvider;
use Dusterio\LumenPassport\LumenPassport;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function boot()
    {
    // Somewhere in your application service provider or bootstrap process
      LumenPassport::allowMultipleTokens();
      # register the routes
      $this->app['path.config'] = base_path('config');

    }


  public function register()
  {

      Schema::defaultStringLength(191);
    $this->app->singleton(Hashids::class, function () {
      return new Hashids('Modullo Production API', 10);
    });



  }
}
