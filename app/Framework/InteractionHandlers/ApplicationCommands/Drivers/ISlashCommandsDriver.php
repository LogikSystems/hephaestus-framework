<?php

namespace App\Framework\InteractionHandlers\ApplicationCommands\Drivers;

use App\Contracts\InteractionDriver;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Illuminate\Support\Collection;

;

interface ISlashCommandsDriver extends InteractionDriver {

    /**
     * Register commands to Discord's API
     */
    public function register() : void;

    /**
     * Get a collection of commands by name
     * @return Collection<string:Command>
     */
    public function getCommandsByName(): Collection;

}
