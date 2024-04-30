<?php

namespace Hephaestus\Framework\Bootstrap;

use Discord\Discord;
use Exception;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\LoggerProxy;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;

class BootstrapDiscord implements BootstrapperContract {


    /**
     * @param Application|HephaestusApplication $app
     */
    public function bootstrap(Application $app): void
    {
        if(!$app instanceof \Hephaestus\Framework\HephaestusApplication) {
            throw new Exception("Cannot bootstrap a non Hephaestus Application.");
        }

        $app->singleton(Discord::class, function () use ($app) {

            $discord = new Discord([
                ...$app['config']['discord'],
                "logger" => new LoggerProxy(),
            ]);

            return $discord;
        });

        $app->singleton('hephaestus.framework.version', function () use ($app) {
            $filePossibleLocation = with($app->make(
                PackageManifest::class
            ))->vendorPath . DIRECTORY_SEPARATOR . 'logiksystems/hephaestus-framework' . DIRECTORY_SEPARATOR . 'composer.json';
            // * If we're in an logiksystems/`hephaestus-application` skeleton `logiksystems/hephaestus-framework`:
            if (File::isDirectory($filePossibleLocation)) {
                return json_decode(File::get($filePossibleLocation))['version'] ?? app('git.version');
            }
            // * If we're in package
            return false;
        });

        $app->afterResolving(Discord::class, fn () => app(LoggerProxy::class)->log('info', 'Resolving discord'));

        // $app->bind(AbstractSlashCommand::class, );
    }

}
