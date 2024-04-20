<?php

namespace App\Contracts;

use App\Framework\Enums\HandledInteractionType;
use Discord\Parts\Interactions\Interaction;

interface InteractionDriver {

    public function getHandledInteractionType(): HandledInteractionType;

    public function find(Interaction $interaction): InteractionHandler|null;
}
