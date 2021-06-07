<?php

namespace App\Providers;

use App\Models\Lms\Courses;
use App\Models\Lms\Programs;
use App\Models\Lms\User;
use App\Models\Lms\Tenants;
use App\Observers\UuidObserver;
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
      User::observe(UuidObserver::class);
      Tenants::observe(UuidObserver::class);
      Programs::observe(UuidObserver::class);
      Courses::observe(UuidObserver::class);
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
