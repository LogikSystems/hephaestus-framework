<?php

namespace Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers;

use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Abstractions\AbstractInteractionDriver;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Collection;
use Monolog\Level;

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
    public abstract function register(): array;

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
