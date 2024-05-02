<?php

namespace Hephaestus\Framework;

use LaravelZero\Framework\Kernel;

use Illuminate\Contracts\Events\Dispatcher;

class HephaestusKernel extends Kernel
{
    protected $bootstrappers = [
        \Hephaestus\Framework\Bootstrap\BootstrapConsoleOutput::class, # Provide

        \LaravelZero\Framework\Bootstrap\CoreBindings::class,
        \LaravelZero\Framework\Bootstrap\LoadEnvironmentVariables::class,
        \LaravelZero\Framework\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \LaravelZero\Framework\Bootstrap\RegisterFacades::class,
        \LaravelZero\Framework\Bootstrap\RegisterProviders::class,

        \Hephaestus\Framework\Bootstrap\BootstrapLoggerProxy::class,

        \Hephaestus\Framework\Bootstrap\BootstrapDiscord::class,

        \Hephaestus\Framework\Bootstrap\BootstrapInteractionRouting::class,

        \Hephaestus\Framework\Bootstrap\RegisterInteractionHandlers::class,

        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];

    /**
     * @inheritdoc
     * @var string[] $commands
     */
    protected $commands = [
        \Hephaestus\Framework\Commands\BootCommand::class,
        \Hephaestus\Framework\Commands\RegisterCommandsCommand::class,
        \Hephaestus\Framework\Commands\ListSlashCommandsCommand::class,
        \Hephaestus\Framework\Commands\HeartbeatCommand::class,

        \Hephaestus\Framework\Commands\ClearLogsCommand::class,

    ];

    /**
     * Global interactions middlewares:
     * @var string[] $commands
     */
    protected $middlewares = [
        \Hephaestus\Framework\Middlewares\MaintenanceMiddleware::class,
    ];

    protected $router;

    public function __construct(
        HephaestusApplication $hephaestusApplication,
        Dispatcher $dispatcher,
    )
    {
        parent::__construct(
            $hephaestusApplication,
            $dispatcher
        );
    }
}
