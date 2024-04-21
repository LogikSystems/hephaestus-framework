<?php

namespace App\InteractionHandlers\MessageComponents;

use App\Framework\InteractionHandlers\MessageComponents\AbstractMessageComponent;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

class TestMessageComponent extends AbstractMessageComponent {

    /**
     *
     */
    public string $component_custom_id = "test";

    public function handle(Interaction $interaction) : void
    {
        // TODO : 👍
        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->setContent("Je suis le Test Message Component Handler.")
        );

    }

}
