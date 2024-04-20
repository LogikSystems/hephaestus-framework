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
class MessageComponentsDriver extends AbstractInteractionDriver
{
    public function getHandledInteractionType(): HandledInteractionType
    {
        return HandledInteractionType::MESSAGE_COMPONENT;
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


}
