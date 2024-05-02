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
    }

    /**
     *  @inheritdoc
     */
    public function boot(): void
    {
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
