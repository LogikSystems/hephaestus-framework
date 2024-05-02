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
use Illuminate\Support\Facades\Process;
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
        // $this->log("info", "Logging discord", [__METHOD__]);

        // $discord->getLoop()->addPeriodicTimer(5, $callback);
    }
}
