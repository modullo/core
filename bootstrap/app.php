<?php

use Nord\Lumen\Cors\CorsServiceProvider;

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);
$app->instance('path.config', app()->basePath() . DIRECTORY_SEPARATOR . 'config');
$app->instance('path.storage', app()->basePath() . DIRECTORY_SEPARATOR . 'storage');

$app->withFacades();

$app->withEloquent();

$app->bind(\Illuminate\Contracts\Routing\UrlGenerator::class, function ($app) {
  return new \Laravel\Lumen\Routing\UrlGenerator($app);
});

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Config Files
|--------------------------------------------------------------------------
|
| Now we will register the "app" configuration file. If the file exists in
| your configuration directory it will be loaded; otherwise, we'll load
| the default version. You may register other files below as needed.
|
*/

$app->configure('app');
$app->configure('auth');
$app->configure('database');
$app->configure('sentry');
$app->configure('cors');
$app->configure('database');
$app->configure('system');
$app->configure('filesystems');
$app->configure('queue');
$app->configure('mail');
$app->configure('setup');
$app->configure('system_permissions');
$app->configure('tenant_permissions');
$app->configure('user_permissions');
$app->configure('roles');
$app->configure('permission');
$app->configure('services');
$app->configure('lms_roles');

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/


 $app->routeMiddleware([
     'auth' => App\Http\Middleware\Authenticate::class,
     'client' => \App\Http\Middleware\ClientMiddleware::class,
     'permission' => Spatie\Permission\Middlewares\PermissionMiddleware::class,
     'role'       => Spatie\Permission\Middlewares\RoleMiddleware::class,
     'lms_user' => \App\Http\Middleware\LmsUserMiddleware::class
 ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
$app->register(Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
$app->register(Spatie\Permission\PermissionServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
$app->register(\App\Providers\ResponseServiceProvider::class);
$app->register(CorsServiceProvider::class);
$app->register(Sentry\Laravel\ServiceProvider::class);
$app->register(\Illuminate\Mail\MailServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(\Fedeisas\LaravelMailCssInliner\LaravelMailCssInlinerServiceProvider::class);
$app->register(Spatie\Permission\PermissionServiceProvider::class);
Dusterio\LumenPassport\LumenPassport::routes($app->router, ['prefix' => 'v1/auth'] );



$app->make('queue');

$app->alias('cache', 'Illuminate\Cache\CacheManager');
$app->alias('auth', 'Illuminate\Auth\AuthManager');
$app->alias('mail.manager', Illuminate\Mail\MailManager::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);



$app->alias('mailer', Illuminate\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\Mailer::class);
$app->alias('mailer', Illuminate\Contracts\Mail\MailQueue::class);
$app->alias('mail.manager', Illuminate\Contracts\Mail\Factory::class);
/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
    require __DIR__.'/../routes/lms.php';
});

return $app;
