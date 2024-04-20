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
use Discord\Parts\Part;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Discord\WebSockets\Event;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Support\Str;
use React\Promise\Promise;

use function React\Async\await;
use function React\Async\coroutine;
use function React\Promise\all;

/**
 * Driver
 */
abstract class AbstractSlashCommandsDriver
extends AbstractInteractionDriver
implements ISlashCommandsDriver
{
    /**
     * @inheritdoc
     */
    public function getHandledInteractionType(): HandledInteractionType
    {
        return HandledInteractionType::APPLICATION_COMMAND;
    }

    /**
     * Get a collection of commands by name
     * @return Collection<string:Command>
     */
    public function getCommandsByName(): Collection
    {
        return $this->getRelatedHandlers()
            ->flatMap(fn (AbstractSlashCommand $class) => [$class->name => $class]);
    }


    /**
     * @inheritdoc
     */
    public abstract function register(): void;

    /**
     * @inheritdoc
     */
    public function find(Interaction $interaction): InteractionHandler|null
    {
        $collect = $this->getRelatedHandlers();
        $commandName = $interaction->data->name;
        $this->hephaestus->log("Received <fg=blue>{$commandName}</> between: <fg=blue>" . $collect->count() . "</> interaction handlers.", Level::Debug, [$interaction]);
        return $collect
            ->first(fn ($c) => strcmp($c->name, $commandName) === 0);
    }
}
