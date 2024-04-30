<?php

namespace Hephaestus\Framework\Commands;

use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event as WsEvent;
use Exception;
use HelpCommand;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\AbstractSlashCommandsDriver;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\ISlashCommandsDriver;
use Hephaestus\Framework\Events\DiscordInteractionEvent;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\Hephaestus;
use Hephaestus\Framework\InteractionHandlers\SlashCommands\HelpSlashCommand;
use Hephaestus\Framework\InteractionReflectionLoader;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Hephaestus\Framework\Models\HelpCommandModel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Signals;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function React\Promise\all;

class BootCommand extends Command
{

    use InteractsWithLoggerProxy;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bot:boot';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Starts the bot';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        $this->log("info", "Logging discord", [__METHOD__]);
        $callback = function (Discord $discord){
            $this->log("info", "Discord is ready, registering slash commands...", [__METHOD__]);
            all($this->app->make(ISlashCommandsDriver::class)->register());

            $discord->on(WsEvent::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
                $this->log("info", "Discord is ready, registering slash commands...", [__METHOD__]);
                Event::dispatch(new DiscordInteractionEvent($interaction, $discord));
            });
        };
        $discord->on('ready', $callback);

        // $discord->getLoop()->addPeriodicTimer(5, $callback);
    }
}
