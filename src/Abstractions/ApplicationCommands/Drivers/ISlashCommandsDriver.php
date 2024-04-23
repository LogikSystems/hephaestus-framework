<?php

namespace Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers;

use Hephaestus\Framework\Contracts\InteractionDriver;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Illuminate\Support\Collection;
use React\Promise\ExtendedPromiseInterface;

;

interface ISlashCommandsDriver extends InteractionDriver {

    /**
     * Register commands to Discord's API
     *
     * @return array<ExtendedPromiseInterface>
     */
    public function register() : array;

    /**
     * Get a collection of commands by name
     * @return Collection<string:Command>
     */
    public function getCommandsByName(): Collection;

}
