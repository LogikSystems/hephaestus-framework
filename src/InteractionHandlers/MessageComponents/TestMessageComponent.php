<?php

namespace Hephaestus\Framework\InteractionHandlers\MessageComponents;

use Hephaestus\Framework\Abstractions\MessageComponents\AbstractMessageComponent;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;

class TestMessageComponent extends AbstractMessageComponent {

    /**
     *
     */
    public string $component_custom_id = "test";

    public function handle(InteractionDTO $interactionDTO) : void
    {
        // TODO : 👍
        // $interaction->respondWithMessage(
        //     MessageBuilder::new()
        //         ->setContent("Je suis le Test Message Component Handler.")
        // );
        $interactionDTO->messageBuilder->setContent("Test");

    }

}
