<?php

namespace Hephaestus\Framework\Middlewares;

use Hephaestus\Framework\Contracts\BaseInteractionMiddleware;
use Closure;
use Discord\Helpers\Collection as DiscordCollection;
use Discord\Parts\Embed\Embed;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;
use Hephaestus\Framework\Hephaestus;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Illuminate\Support\Str;

class MaintenanceMiddleware extends BaseInteractionMiddleware
{
    public function handle(InteractionDTO $interactionDTO, Closure $next): mixed
    {
        $guildsBypassingMaintenanceMode = config('hephaestus.bypass_maintenance_guild_ids', []);

        $guildsBypassingStr = collect($guildsBypassingMaintenanceMode)
            ->map(
                fn (mixed $guild_id) => "- `{$guild_id}`",
            )->join("\n");

        $errorMessage = Str::of("🚧 Here are the only guilds i'm able to handle now : \n")
            ->append($guildsBypassingStr);

        $stateIsApplicationDown = app()->isDownForMaintenance();
        $stateInBypassIdArray = in_array($interactionDTO->interaction->guild_id, $guildsBypassingMaintenanceMode);


        $strMode = $stateIsApplicationDown ? 'ON' : "OFF";
        $emoji = $stateIsApplicationDown ? "🟠" : "🟢";

        $interactionDTO->messageBuilder->addEmbed(
            new Embed($interactionDTO->discord, [
                "title" => "{$emoji} `MAINTENANCE MODE` IS `{$strMode}`",
                "description" =>
                $stateIsApplicationDown
                    ? ($stateInBypassIdArray ? "This guild is handled under maintenance mode." : $errorMessage)
                    : "Everything is ok 🫡",
                "color" => $stateIsApplicationDown ? 11027200 : 5763719,
                "fields" => new DiscordCollection([]),
            ])
        );

        return app()->isDownForMaintenance()
            ? ($stateInBypassIdArray ? $next($interactionDTO) : false)
            : $next($interactionDTO);
    }
}
