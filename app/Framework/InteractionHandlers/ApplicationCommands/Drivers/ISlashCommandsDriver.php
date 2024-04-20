<?php

namespace App\Framework\InteractionHandlers\ApplicationCommands\Drivers;;

interface ISlashCommandsDriver {

    /**
     * Register commands to Discord's API
     */
    public function register() : void;



}
