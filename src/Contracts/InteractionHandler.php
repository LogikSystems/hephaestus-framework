<?php

namespace Hephaestus\Framework\Contracts;

use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;
use Hephaestus\Framework\InteractsWithLoggerProxy;

interface InteractionHandler {

    /**
     * Handler for an interaction
     *
     * @return void
     */
    public function handle(InteractionDTO $interaction): void;

    public function getDiscriminator(): string;

    public function getDiscriminatorAttributeName(): string;
}
