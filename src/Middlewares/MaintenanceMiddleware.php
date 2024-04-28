<?php

namespace Hephaestus\Framework\Middlewares;

use Hephaestus\Framework\Contracts\BaseInteractionMiddleware;
use Closure;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;

class MaintenanceMiddleware extends BaseInteractionMiddleware
{
    public function handle(InteractionDTO $interactionDTO): bool
    {
        $guildsBypassingMaintenanceMode = config('hephaestus.bypass_maintenance_guild_ids', []);

        $state = app()->isDownForMaintenance()
            && !in_array($interactionDTO->interaction->guild_id, $guildsBypassingMaintenanceMode);

        if ($state) {
            $interactionDTO->messageBuilder->setContent(
                "Sorry i'm in maintenance mode and i can't respond to you now."
            );
        }

        return $state;
    }
}
