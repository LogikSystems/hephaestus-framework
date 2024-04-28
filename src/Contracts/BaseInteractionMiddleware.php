<?php

namespace Hephaestus\Framework\Contracts;

use Closure;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;
use Hephaestus\Framework\InteractsWithLoggerProxy;

abstract class BaseInteractionMiddleware {
    use InteractsWithLoggerProxy;
    /**
     * Handler for an interaction
     *
     * @return void
     */
    public function handle(InteractionDTO $interaction, Closure $next): mixed
    {}

    public function __construct()
    {
        $name = class_basename($this);
        $this->log("debug", "Constructing <fg=cyan>{$name}</>", [__METHOD__]);
    }
}
