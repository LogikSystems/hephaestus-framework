<?php

namespace App\Bot\InteractionHandlers;

use App\Bot\InteractionHandlers\HandledInteractionType;
use App\Bot\InteractionHandlers\SlashCommands\AbstractSlashCommand;
use App\Contracts\InteractionDriver;
use App\Contracts\InteractionHandler;
use App\Hephaestus;
use Discord\Builders\CommandBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\OAuth\Application;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Discord\WebSockets\Event;
use Illuminate\Support\Facades\Cache;
use Monolog\Level;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Support\Str;

use function React\Async\await;

/**
 * Driver
 */
class SlashCommandsDriver implements InteractionDriver
{
    public function __construct(
        public Hephaestus $hephaestus,
    ) {
    }

    // /**
    //  * @return array
    //  */
    // public function getHandlersClasses()
    // {
    //     return $this->hephaestus->loader->hydratedHandlers(HandledInteractionType::APPLICATION_COMMAND);
    // }

    public function register()
    {

        $commands = $this->hephaestus->loader->hydratedHandlers(HandledInteractionType::APPLICATION_COMMAND)
            // ->map(fn (string $className))
            ->map(fn (Command $class) => $class)
            ->keyBy(fn (Command $class) => $class->name)
            ->toArray(); // ['command-name' => 'Part']

        $count = count($commands);

        $this->hephaestus->discord->application->commands->freshen()->done(function (GlobalCommandRepository $gcr) use ($commands, $count) {
            $this->hephaestus->log("Registering <fg=cyan>{$count}</> application slash commands", Level::Debug, [$gcr, $commands]);
            foreach ($gcr as $command) {
                if (!array_key_exists($command->name, $commands)) {
                    $this->hephaestus->log("Removing {$command->id}", Level::Debug, [$command, $this->hephaestus->discord->application->commands]);
                    $this->hephaestus->discord->application->commands->delete($command);
                }
            }

            var_dump($commands);
            foreach ($commands as $commandName => $command) {
                // $this->hephaestus->log("test {$commandName}");
                $this->hephaestus->log("[HEPHAESTUS] | Creating or updating {$commandName}");
                // $gcr = await($this->hephaestus->discord->application->commands->freshen());
                $this->hephaestus->discord->application->commands->save(
                    new Command(
                        $this->hephaestus->discord,
                        CommandBuilder::new()
                            ->setName("test")
                            ->setDescription("test test")
                            ->setType(Command::CHAT_INPUT)
                            ->toArray()

                    )
                )
                    ->done(
                        onFulfilled: fn () => app(Hephaestus::class)->log("$commandName saved"),
                        onRejected: fn () => app(Hephaestus::class)->log("$commandName not saved", Level::Critical)
                    );
                // $this->hephaestus->log("[HEPHAESTUS] | Saved {$commandName}");
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function find(Interaction $interaction): InteractionHandler|null
    {
        $collect = $this->hephaestus->loader->hydratedHandlers($this->getHandledInteractionType());
        $this->hephaestus->log("Filtering between: <fg=blue>".$collect->count()."</> interaction handlers.", Level::Debug, [$interaction]);
        return $this->hephaestus->loader->hydratedHandlers($this->getHandledInteractionType())

            ->filter(fn ($c) => strcmp($c->name, $interaction->data->name) === 0)
            ->first();
    }

    public function getHandledInteractionType(): HandledInteractionType
    {
        return HandledInteractionType::APPLICATION_COMMAND;
    }
}
