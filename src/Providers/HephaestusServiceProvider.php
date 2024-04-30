<?php

namespace Hephaestus\Framework\Providers;

use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\ISlashCommandsDriver;
use Hephaestus\Framework\Abstractions\HephaestusApplication;
use Hephaestus\Framework\Hephaestus;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\OutputInterface;

class HephaestusServiceProvider extends ServiceProvider
{

    use InteractsWithLoggerProxy;

    private function helperConfigPathName(string $fileName) : string
    {
        return  __DIR__ .
            DIRECTORY_SEPARATOR .
            ".." .
            DIRECTORY_SEPARATOR .
            ".." .
            DIRECTORY_SEPARATOR .
            "config" .
            DIRECTORY_SEPARATOR .
            $fileName;
    }

    private function packageRootPathName(string $fileName) : string
    {
        return  __DIR__ .
        DIRECTORY_SEPARATOR .
        ".." .
        DIRECTORY_SEPARATOR .
        ".." .
        DIRECTORY_SEPARATOR .
        $fileName;
    }

    /**
     * @inheritdoc
     */
    public function register(): void
    {

        $this->mergeConfigFrom(
            $this->helperConfigPathName('app.php'),
            "app"
        );
        $this->mergeConfigFrom(
            $this->helperConfigPathName('hephaestus.php'),
            "hephaestus"
        );

        $this->mergeConfigFrom(
            $this->helperConfigPathName('discord.php'),
            "discord"
        );

        $this->app->singleton(Hephaestus::class, fn () => \Hephaestus\Framework\Hephaestus::make(
            output: app(OutputInterface::class),
        ));

        $this->app->singleton(ISlashCommandsDriver::class, function () {
            $className = config('hephaestus.drivers.APPLICATION_COMMAND');
            return app($className, ['hephaestus' => app(Hephaestus::class)]);
        });
    }

    /**
     *  @inheritdoc
     */
    public function boot(): void
    {
        // Collection::macro("version", function () {
        //     return json_decode(require_once(base_path('composer.json')))->version;
        // });

        $this->booted(function () {
            $this->log("info", "Service provider booted");
        });
        $this->wrapPublishConfigs(
            "app",
            "discord",
            "hephaestus",
            "logging"
        );


        $this->publishes([
            $this->packageRootPathName('Dockerfile')            => base_path('Dockerfile'),
            $this->packageRootPathName('docker-compose.yml')    => base_path('docker-compose.yml')
        ], 'hephaestus-docker');

        $this->publishes([
            $this->packageRootPathName('resources/views/components') => base_path('resources/views/components')
        ], 'hephaestus-views');

        $this->commands([
            \Hephaestus\Framework\Commands\ListSlashCommandsCommand::class,
            \Hephaestus\Framework\Commands\BootCommand::class,
            \Hephaestus\Framework\Commands\ClearLogsCommand::class
        ]);
    }

    public function wrapPublishConfig(string $normalizedAlias)
    {
        $filename = "{$normalizedAlias}.php";
        $this->publishes([
            $this->helperConfigPathName($filename) => config_path($filename),
        ], "hephaestus-configs");
    }

    /**
     *
     */
    public function wrapPublishConfigs(string ...$normalizedAlias) {
        foreach($normalizedAlias as $alias) {
            $this->wrapPublishConfig($alias);
        }
    }
}
