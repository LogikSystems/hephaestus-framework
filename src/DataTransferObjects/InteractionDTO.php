<?php

namespace Hephaestus\Framework\DataTransferObjects;

use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\Interactions\Request\InteractionData;

class InteractionDTO {

    public function __construct(
        public InteractionData $interaction,
        public Discord $discord,
        public ?MessageBuilder $messageBuilder = new MessageBuilder(),
    )
    {

    }


}
