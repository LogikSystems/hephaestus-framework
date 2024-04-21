<?php

namespace App\Framework\InteractionHandlers\MessageComponents;

use App\Contracts\InteractionHandler;
use Discord\Parts\Interactions\Interaction;

abstract class AbstractMessageComponent implements InteractionHandler {

    public string $component_custom_id;

    public abstract function handle(Interaction $interaction) : void;

}
