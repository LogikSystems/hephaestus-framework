<?php

namespace App\Framework\InteractionHandlers\ApplicationCommands\Drivers;

use App\Contracts\InteractionDriver;
use Discord\Repository\Interaction\GlobalCommandRepository;

;

interface ISlashCommandsDriver extends InteractionDriver {

    /**
     * Register commands to Discord's API
     */
    public function register() : void;


}
