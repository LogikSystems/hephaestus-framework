<?php

namespace Hephaestus\Framework\Bootstrap;

use Exception;
use Hephaestus\Framework\Events\ApplicationChangeMaintenanceMode;
use Hephaestus\Framework\Events\DiscordInteractionEvent;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\InteractionReflectionLoader;
use Hephaestus\Framework\Listeners\ApplicationChangeMaintenanceModeListener;
use Hephaestus\Framework\Listeners\DiscordInteractionEventListener;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Reflector;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use Symfony\Component\EventDispatcher\EventDispatcher;

class RegisterInteractionHandlers implements BootstrapperContract {


    /**
     * @param Application|HephaestusApplication $app
     */
    public function bootstrap(Application $app): void
    {
        if(!$app instanceof \Hephaestus\Framework\HephaestusApplication) {
            throw new Exception("Cannot bootstrap a non Hephaestus Application.");
        }

        // TODO
        // Reload interaction handlers
        // $app->bin
        $app->singleton(
            InteractionReflectionLoader::class,
            fn () => new InteractionReflectionLoader($app)
        );

        // $app->make(Dispatcher::class)
        // $app->make(Kernel)
        /**
         * @var Dispatcher $dispatcher
         **/
        $dispatcher = $app[Dispatcher::class];

        $dispatcher->listen(DiscordInteractionEvent::class, DiscordInteractionEventListener::class);

        $dispatcher->listen(ApplicationChangeMaintenanceMode::class, ApplicationChangeMaintenanceModeListener::class);

    }

}
