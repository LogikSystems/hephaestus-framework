<?php

namespace Hephaestus\Framework\Bootstrap;

use Discord\Discord;
use Exception;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\AbstractSlashCommandsDriver;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\ISlashCommandsDriver;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\InteractionReflectionLoader;
use Hephaestus\Framework\LoggerProxy;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use Symfony\Component\Console\Output\BufferedOutput;

class BootstrapInteractionRouting implements BootstrapperContract
{
    /**
     * @param Application|HephaestusApplication $app
     */
    public function bootstrap(Application $app): void
    {
        $app->singleton(
            InteractionReflectionLoader::class,
            fn () => new InteractionReflectionLoader($app)
        );
        // dd($app['config']['hephaestus.drivers']['APPLICATION_COMMAND']);
        $app->singleton(
            AbstractSlashCommandsDriver::class,
            fn () => $app->make($app['config']['hephaestus.drivers']['APPLICATION_COMMAND'])
        );
    }
}
