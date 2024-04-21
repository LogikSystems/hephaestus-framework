<?php

namespace App\Framework\InteractionHandlers\MessageComponents\Drivers;


use App\Contracts\InteractionHandler;
use App\Framework\Enums\HandledInteractionType;
use App\Framework\InteractionHandlers\AbstractInteractionDriver;
use Discord\Parts\Interactions\Interaction;
use Monolog\Level;

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
