<?php

namespace Hephaestus\Framework\Commands;


use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\ISlashCommandsDriver;
use Hephaestus\Framework\Hephaestus;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Hephaestus\Framework\InteractionReflectionLoader;
use Illuminate\Console\Command;

class ListSlashCommandsCommand extends Command
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
        $hephaestus = app(Hephaestus::class);
        // $hephaestus->setOutput($this->output);

        /**
         * @var ISlashCommandsDriver driver
         */
        $driver = app(ISlashCommandsDriver::class);

        // $hephaestus->command->writeln("caca");
        // dd($driver->hephaestus->loader->extractClasses(HandledInteractionType::APPLICATION_COMMAND));

        $this->drawCommandTable($hephaestus);

        // $hephaestus->discord = new Discord([
        //     'token'     => config('discord.token'),
        //     'intents'   => config('discord.intents'),
        //     // 'logger'    =>
        //     // 'loop'      => \React\EventLoop\Factory::create(),
        // ]);

        // $hephaestus->discord->on('ready', fn () => $driver->register());


        // $commands = $driver->getCommandsByName();

        // $hephaestus->discord->on('ready', function (Discord $discord) use ($hephaestus, $driver, $commands) {

        //     $gcr = await($discord->application->commands->freshen());
        //     $this->output->writeln("<fg=red>" . $gcr->count() . "</>");
        //     $count = $commands->count();
        //     $discord->application->commands
        //         ->freshen()
        //         ->done(
        //             onFulfilled: fn (GlobalCommandRepository $repo) => $this->testUpdate($repo),
        //             onRejected: function () {
        //                 $this->output->writeln("<fg=red>Can't refresh GlobalCommandsRepository</>");
        //             }
        //         );

        //     $this->drawCommandTable($hephaestus);
        // });
    }

    public function drawCommandTable(Hephaestus $hephaestus)
    {

        /**
         * @var ISlashCommandsDriver
         */
        $slashCommandDriver = app(ISlashCommandsDriver::class);

        /**
         * @var InteractionReflectionLoader
         */
        $loader = app(Hephaestus::class)->loader;

        dd($loader->getClasses($slashCommandDriver->getHandledInteractionType()));

        $slashCommandDriverCommands = $slashCommandDriver
            ->getCommandsByName()
            ->map(fn ($c) => ["Command Name" => $c->name, "Description" => $c->description])
            ->sortBy("Command Name", SORT_STRING, SORT_ASC);

        $this->output->table(["Command Name", "Description"], $slashCommandDriverCommands->toArray());
    }


    public function testUpdate(GlobalCommandRepository $gcr)
    {
        // /**
        //  * @var AbstractSlashCommandsDriver driver
        //  */
        // $driver = app(AbstractSlashCommandsDriver::class);
        // /**
        //  * @var Hephaestus hephaestus
        //  */
        // $hephaestus = app(Hephaestus::class);
        // $gcrCount = $gcr->count();

        // /**
        //  * @var Collection<string,CommandCommand> $commands
        //  */
        // $commands = $driver->getCommandsByName();
        // $count = $commands->count();

        // $this->output->writeln(
        //     "Refreshing {$gcrCount} commands, found {$count} application slash commands."
        // );
        // // try {
        // foreach ($gcr as $gcr_command) {
        //     $this->output->writeln("Removing {$gcr_command->id}");
        //     if (!$commands->has($gcr_command->name)) {
        //         $hephaestus->discord->application->commands->delete($gcr_command)
        //             ->done(onFulfilled: function () use ($gcr_command) {
        //                 $this->output->writeln("<fg=green>Deleted command {$gcr_command->name}</>");
        //             }, onRejected: function () use ($gcr_command) {
        //                 $this->output->writeln("<fg=red>>Rejected deletion of command {$gcr_command->name}</>");
        //             });
        //     }
        // }

        // foreach ($commands as $commandName => $command) {

        //     $hephaestus->log("Iterating through COMMANDS, currently on {$commandName}");
        //     $c = new CommandCommand($hephaestus->discord, CommandBuilder::new()
        //         ->setName($command->name)
        //         ->setDescription($command->description)
        //         ->setType(CommandCommand::CHAT_INPUT)
        //         ->toArray());
        //     $a = $gcr
        //         ->save($c)
        //         ->done(
        //             onFulfilled: fn ()  => $this->output->writeln("<fg=green>Added or upddated {$commandName}</>"),
        //             onRejected: fn ()   => $this->output->writeln("<fg=red>Can't add or update {$commandName}</>")
        //         );
        // }
        // } catch (Exception $e) {
        //     throw $e;
        // }
    }
}
