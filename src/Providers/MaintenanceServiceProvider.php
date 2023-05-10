<?php

namespace Odisse\Maintenance\Providers;

// use App\View\Components\Sample;

use App\Http\Middleware\AuthenticatedUsersMiddleware;
use App\Http\Middleware\SettingsLoader;
use App\Http\Middleware\SSO;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Odisse\Maintenance\Facades\MaintenanceServiceFacade;
use Odisse\Maintenance\Repositories\MaintenanceRepository;
use Odisse\Maintenance\Services\MaintenanceService;
use Odisse\Maintenance\View\Components\Sample;

class MaintenanceServiceProvider extends ServiceProvider
{

    // protected $middlewareAliases = [
    //     'ProxyCAS' => SSO::class,
    //     'SettingLoader' => SettingsLoader::class,
    //     'AuthenticatedUsersMiddleware', AuthenticatedUsersMiddleware::class
    // ];

    public function boot()
    {

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/web.php');
        $this->loadViewsFrom(dirname(__DIR__)."/Resources/views/",'maintenance');
        $this->loadMigrationsFrom(dirname(__DIR__)."/../database/migrations/",'maintenance');
        // $this->loadViewComponentsAs(dirname(__DIR__)."/Resources/views/",'maintenance');
        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang/','maintenance');

        Blade::anonymousComponentPath(dirname(__DIR__).'/Resources/Components','maintenance');


        $this->loadViewComponentsAs("maintenance",
        [
            Sample::class,
        ]);

        // $this->aliasMiddleware();
        $this->offerPublishing();
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('ProxyCAS', SSO::class);
        $router->aliasMiddleware('settingsLoader', SettingsLoader::class);
        $router->aliasMiddleware('AuthenticatedUsersMiddleware', AuthenticatedUsersMiddleware::class);

    }

    public function register()
    {

        //to merge confg of this pacckage with the main config of laravel
        $this->mergeConfigFrom(__DIR__.'/../../config/Maintenance.php','maintenances');

        $this->app->bind('OdisseMaintenances', function () {

            return new Manintenace;
        });

        $this->app->singleton('MaintenanceRepository', MaintenanceRepository::class);


        $this->app->singleton('OdisseMaintenances', function ($app) {
            $MaintenanceService = new MaintenanceService($app['MaintenanceRepository']);

            return $MaintenanceService;
        });

        $loader = AliasLoader::getInstance();
        $loader->alias('OdisseMaintenances', MaintenanceServiceFacade::class);

    }

    protected function offerPublishing()
    {
        if (! function_exists('config_path')) {
            // function not available and 'publish' not relevant in Lumen
            return;
        }

        $this->publishes([
            __DIR__.'/../../config/Maintenance.php' => config_path('Maintenance.php'),
        ], 'config');

        // $this->publishes([
        //     __DIR__.'/../../database/migrations/create_Maintenance_tables.php.stub' => $this->getMigrationFileName('create_Maintenance_tables.php'),
        // ], 'migrations');
    }

    protected function registerCommands()
    {
        $this->commands([]);
    }

        /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @return string
     */
    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }

    protected function aliasMiddleware()
    {

        $router = $this->app['router'];

        $method = method_exists($router, 'aliasMiddleware') ? 'aliasMiddleware' : 'middleware';


        foreach ($this->middlewareAliases as $alias => $middleware) {
            $router->$method($alias, $middleware);
        }
    }
}
