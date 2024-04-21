<?php

namespace App\Contracts;

use Discord\Parts\Interactions\Interaction;

interface InteractionHandler {

    public function handle(Interaction $interaction): void;

}
