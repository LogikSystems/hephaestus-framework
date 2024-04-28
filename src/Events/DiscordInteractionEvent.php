<?php

namespace Hephaestus\Framework\Events;

use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Illuminate\Foundation\Events\Dispatchable;

class DiscordInteractionEvent
{
    use Dispatchable;

    public function __construct(
        public Interaction $interaction,
        public Discord $discord
    )
    {
    }

    public function getType(): HandledInteractionType
    {
        return HandledInteractionType::from($this->interaction->type);
    }
}
