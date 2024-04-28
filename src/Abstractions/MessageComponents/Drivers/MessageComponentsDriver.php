<?php

namespace Hephaestus\Framework\Abstractions\MessageComponents\Drivers;


use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Abstractions\AbstractInteractionDriver;
use Hephaestus\Framework\Abstractions\MessageComponents\AbstractMessageComponent;
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
        // dd($interaction);
        $collect = $this->getRelatedHandlers();
        // dd($collect->first());
        $this->hephaestus->log("Filtering between: <fg=blue>".$collect->count()."</> interaction handlers.", Level::Debug, [$interaction]);
        return $collect
            // ->each(fn ($class) => $class)
            ->first(fn (AbstractMessageComponent $c) => strcmp($c->component_custom_id, $interaction->data?->custom_id) === 0);
    }
}
