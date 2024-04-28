<?php

namespace Hephaestus\Framework\Providers;

use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\ISlashCommandsDriver;
use Hephaestus\Framework\Abstractions\HephaestusApplication;
use Hephaestus\Framework\Hephaestus;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Output\OutputInterface;

class HephaestusServiceProvider extends ServiceProvider
{

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
            "hephaestus-app"
        );

        $this->mergeConfigFrom(
            $this->helperConfigPathName('discord.php'),
            "hephaestus-discord"
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
        $this->publishes([
            $this->helperConfigPathName('discord.php') => config_path('discord.php'),
        ], 'hephaestus-config');

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
}
