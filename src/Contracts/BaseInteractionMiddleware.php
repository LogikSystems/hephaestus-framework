<?php

namespace Hephaestus\Framework\Contracts;

use Closure;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;

abstract class BaseInteractionMiddleware {

    /**
     * Handler for an interaction
     *
     * @return void
     */
    public abstract function handle(InteractionDTO $interaction): bool;
}
