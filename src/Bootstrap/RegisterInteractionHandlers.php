<?php

namespace Hephaestus\Framework\Bootstrap;

use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event as WebSocketsEvent;
use Exception;
use Hephaestus\Framework\Events\ApplicationChangeMaintenanceMode;
use Hephaestus\Framework\Events\DiscordInteractionEvent;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\InteractionReflectionLoader;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Hephaestus\Framework\Listeners\ApplicationChangeMaintenanceModeListener;
use Hephaestus\Framework\Listeners\DiscordInteractionEventListener;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Reflector;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;

use function React\Promise\all;

class RegisterInteractionHandlers implements BootstrapperContract {

    use InteractsWithLoggerProxy;

    /**
     * @param Application|HephaestusApplication $app
     */
    public function bootstrap(Application $app,): void
    {
        if(!$app instanceof \Hephaestus\Framework\HephaestusApplication) {
            throw new Exception("Cannot bootstrap a non Hephaestus Application.");
        }

        $discord = app(Discord::class);

        $callback = function () use ($discord, $app){
            Event::dispatch(new ApplicationChangeMaintenanceMode(app()->isDownForMaintenance()));
        };
        $discord->on('ready', $callback);

        $discord->on(WebSocketsEvent::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
            $this->log("info", "Discord is ready, registering slash commands...", [__METHOD__]);
            Event::dispatch(new DiscordInteractionEvent($interaction, $discord));
        });
        $discord->on('heartbeat', function () {
            app()->log("info", "Sending heartbeat to discord...");
        });

        $discord->on('heartbeat-ack', function () {
            app()->log("info", "Received heartbeat from discord...");
        });
        /**
         * @var Dispatcher $dispatcher
         **/
        $dispatcher = $app[Dispatcher::class];

        $dispatcher->listen(DiscordInteractionEvent::class, DiscordInteractionEventListener::class);
        $dispatcher->listen(ApplicationChangeMaintenanceMode::class, ApplicationChangeMaintenanceModeListener::class);
    }

}
