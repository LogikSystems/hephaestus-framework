<?php

namespace App\Commands;

use App\Bot\InteractionHandlers\AbstractInteractionDriver;
use App\Bot\InteractionHandlers\AbstractSlashCommandsDriver;
use App\Bot\InteractionHandlers\HandledInteractionType;
use App\Bot\InteractionHandlers\ISlashCommandsDriver;
use App\Bot\InteractionHandlers\SlashCommandsDriver;
use App\Hephaestus;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command as CommandCommand;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

use function React\Async\async;
use function React\Async\await;

class Commands extends Command
{

    // use Logs;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bot:commands';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Echo the bot commands';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /**
         * @var Hephaestus
         */
        $hephaestus = $this->app->make(Hephaestus::class);
        // $hephaestus->setOutput($this->output);

        /**
         * @var AbstractSlashCommandsDriver driver
         */
        $driver = app(AbstractSlashCommandsDriver::class);

        $hephaestus->discord = new Discord([
            'token'     => config('discord.token'),
            'intents'   => config('discord.intents'),
            // 'logger'    =>
            // 'loop'      => \React\EventLoop\Factory::create(),
        ]);

        $commands = $driver->getCommandsByName();

        $hephaestus->discord->on('ready', function (Discord $discord) use ($hephaestus, $driver, $commands) {

            $gcr = await($discord->application->commands->freshen());
            $this->output->writeln("<fg=red>" . $gcr->count() . "</>");
            $count = $commands->count();
            $discord->application->commands
                ->freshen()
                ->done(
                    onFulfilled: fn (GlobalCommandRepository $repo) => $this->testUpdate($repo),
                    onRejected: function () {
                        $this->output->writeln("<fg=red>Can't refresh GlobalCommandsRepository</>");
                    }
                );

            $this->drawCommandTable($hephaestus);
        });
    }

    public function drawCommandTable(Hephaestus $hephaestus)
    {
        $slashCommandDriverCommands = $hephaestus->loader->getDriver(HandledInteractionType::APPLICATION_COMMAND)
            ->getRelatedHandlers()
            ->map(fn ($c) => ["Command Name" => $c->name, "Description" => $c->description])
            ->sortBy("Command Name", SORT_STRING, SORT_ASC);

        $this->output->table(["Command Name", "Description"], $slashCommandDriverCommands->toArray());
    }


    public function testUpdate(GlobalCommandRepository $gcr)
    {
        /**
         * @var AbstractSlashCommandsDriver driver
         */
        $driver = app(AbstractSlashCommandsDriver::class);
        /**
         * @var Hephaestus hephaestus
         */
        $hephaestus = app(Hephaestus::class);
        $gcrCount = $gcr->count();

        /**
         * @var Collection<string,CommandCommand> $commands
         */
        $commands = $driver->getCommandsByName();
        $count = $commands->count();

        $this->output->writeln(
            "Refreshing {$gcrCount} commands, found {$count} application slash commands."
        );
        // try {
        foreach ($gcr as $gcr_command) {
            $this->output->writeln("Removing {$gcr_command->id}");
            if (!$commands->has($gcr_command->name)) {
                $hephaestus->discord->application->commands->delete($gcr_command)
                    ->done(onFulfilled: function () use ($hephaestus) {
                        $this->output->writeln("<fg=green>Valid command</>");
                    }, onRejected: function () use ($hephaestus) {
                        $this->output->writeln("Rejected");
                    });
            }
        }

        foreach ($commands as $commandName => $command) {

            $hephaestus->log("Iterating through COMMANDS, currently on {$commandName}");
            $c = new CommandCommand($hephaestus->discord, CommandBuilder::new()
                ->setName($command->name)
                ->setDescription($command->description)
                ->setType(CommandCommand::CHAT_INPUT)
                ->toArray());
            $a = $gcr
                ->save($c)
                ->done(onFulfilled: fn () => $this->output->writeln("Youhou"), onRejected: fn () => $this->output->writeln(":( !"));
            var_dump($a);
        }
        // } catch (Exception $e) {
        //     throw $e;
        // }
    }
}
