<?php

declare(strict_types=1);

namespace codicastudio\Statistics\Providers;

use Illuminate\Routing\Router;
use codicastudio\Statistics\Models\Path;
use codicastudio\Statistics\Models\Agent;
use codicastudio\Statistics\Models\Datum;
use codicastudio\Statistics\Models\Geoip;
use codicastudio\Statistics\Models\Route;
use codicastudio\Statistics\Models\Device;
use codicastudio\Statistics\Models\Request;
use codicastudio\Statistics\Models\Platform;
use Illuminate\Support\ServiceProvider;
use codicastudio\Support\Traits\ConsoleTools;
use codicastudio\Statistics\Console\Commands\MigrateCommand;
use codicastudio\Statistics\Console\Commands\PublishCommand;
use codicastudio\Statistics\Http\Middleware\TrackStatistics;
use codicastudio\Statistics\Console\Commands\RollbackCommand;

class StatisticsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.codicastudio.statistics.migrate',
        PublishCommand::class => 'command.codicastudio.statistics.publish',
        RollbackCommand::class => 'command.codicastudio.statistics.rollback',
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'codicastudio.statistics');

        // Bind eloquent models to IoC container
        $this->app->singleton('codicastudio.statistics.datum', $datumModel = $this->app['config']['codicastudio.statistics.models.datum']);
        $datumModel === Datum::class || $this->app->alias('codicastudio.statistics.datum', Datum::class);

        $this->app->singleton('codicastudio.statistics.request', $requestModel = $this->app['config']['codicastudio.statistics.models.request']);
        $requestModel === Request::class || $this->app->alias('codicastudio.statistics.request', Request::class);

        $this->app->singleton('codicastudio.statistics.agent', $agentModel = $this->app['config']['codicastudio.statistics.models.agent']);
        $agentModel === Agent::class || $this->app->alias('codicastudio.statistics.agent', Agent::class);

        $this->app->singleton('codicastudio.statistics.geoip', $geoipModel = $this->app['config']['codicastudio.statistics.models.geoip']);
        $geoipModel === Geoip::class || $this->app->alias('codicastudio.statistics.geoip', Geoip::class);

        $this->app->singleton('codicastudio.statistics.route', $routeModel = $this->app['config']['codicastudio.statistics.models.route']);
        $routeModel === Route::class || $this->app->alias('codicastudio.statistics.route', Route::class);

        $this->app->singleton('codicastudio.statistics.device', $deviceModel = $this->app['config']['codicastudio.statistics.models.device']);
        $deviceModel === Device::class || $this->app->alias('codicastudio.statistics.device', Device::class);

        $this->app->singleton('codicastudio.statistics.platform', $platformModel = $this->app['config']['codicastudio.statistics.models.platform']);
        $platformModel === Platform::class || $this->app->alias('codicastudio.statistics.platform', Platform::class);

        $this->app->singleton('codicastudio.statistics.path', $pathModel = $this->app['config']['codicastudio.statistics.models.path']);
        $pathModel === Path::class || $this->app->alias('codicastudio.statistics.path', Path::class);

        // Register console commands
        $this->registerCommands($this->commands);
    }

    /**
     * {@inheritdoc}
     */
    public function boot(Router $router)
    {
        // Publish Resources
        $this->publishesConfig('codicastudio/statistics');
        $this->publishesMigrations('codicastudio/statistics');
        ! $this->autoloadMigrations('codicastudio/statistics') || $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');

        // Push middleware to web group
        $router->pushMiddlewareToGroup('web', TrackStatistics::class);
    }
}
