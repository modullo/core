<?php

namespace App\Providers;

use App\Models\Lms\Assets;
use App\Models\Lms\Courses;
use App\Models\Lms\Lessons;
use App\Models\Lms\Modules;
use App\Models\Lms\Programs;
use App\Models\Lms\Quiz;
use App\Models\Lms\Tenants;
use App\Models\Lms\User;
use App\Observers\UuidObserver;
use Dusterio\LumenPassport\LumenPassport;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Modules::observe(UuidObserver::class);
        Assets::observe(UuidObserver::class);
        Lessons::observe(UuidObserver::class);
        Quiz::observe(UuidObserver::class);
        // Somewhere in your application service provider or bootstrap process
        LumenPassport::allowMultipleTokens();
        # register the routes
        $this->app['path.config'] = base_path('config');
    }

    public function register()
    {
        Schema::defaultStringLength(191);
    }
}
