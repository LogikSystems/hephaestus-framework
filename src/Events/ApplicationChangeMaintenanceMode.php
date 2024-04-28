<?php

namespace Hephaestus\Framework\Events;

use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\HephaestusApplication;
use Illuminate\Foundation\Events\Dispatchable;

class ApplicationChangeMaintenanceMode
{
    use Dispatchable;

    public function __construct(
        // public HephaestusApplication $hephaestusApplication,
        public bool $newValue,
        public bool|null $oldValue = null,
    )
    {

    }
}
