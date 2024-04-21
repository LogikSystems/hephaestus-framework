<?php

namespace App\Contracts;

use App\Framework\Enums\HandledInteractionType;
use Discord\Parts\Interactions\Interaction;

interface InteractionDriver {

    /**
     * Returns the type this InteractionDriver handles
     *
     * @return HandledInteractionType
     */
    public function getHandledInteractionType(): HandledInteractionType;

    /**
     * Retrieve a designated handler
     * for the interaction passed in params:
     *
     * - Drivers implement ISlashCommandsDriver MAY identify them using data->name
     * - Message components drivers MAY identify them using data->components->name ? Maybe
     * - Ping drivers MAY return the first implementation on cache because there's no
     * point having multiple handlers ?
     */
    public function find(Interaction $interaction): InteractionHandler|null;
}
