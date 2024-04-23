<?php

namespace Hephaestus\Framework\Abstractions\MessageComponents;

use Hephaestus\Framework\Contracts\InteractionHandler;
use Discord\Parts\Interactions\Interaction;

abstract class AbstractMessageComponent implements InteractionHandler {

    public string $component_custom_id;

    public abstract function handle(Interaction $interaction) : void;

}
