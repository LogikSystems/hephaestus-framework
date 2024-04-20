<?php

namespace App\Bot\InteractionHandlers;

interface ISlashCommandsDriver {

    /**
     * Register commands to Discord's API
     */
    public function register() : void;



}
