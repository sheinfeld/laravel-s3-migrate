<?php

namespace Sheinfeld\S3Migration;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Sheinfeld\S3Migration\Commands\MigrateCommand;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind('file-collector', function ($app) {
            return new FileCollector(
                new FilesystemManager($app)
            );
        });

        $this->app->bind('s3-migrator', function ($app) {
            return new Migrator();
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateCommand::class,
            ]);
        }

        $this->mergeConfigFrom(
            __DIR__.'/config/s3migrate.php', 's3migrate'
        );
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/s3migrate.php' => config_path('s3migrate.php'),
        ], 's3migrate-config');
    }
}
