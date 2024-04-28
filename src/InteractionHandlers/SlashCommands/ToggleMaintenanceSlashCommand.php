<?php

namespace Hephaestus\Framework\InteractionHandlers\SlashCommands;

use Discord\Builders\Components\Option;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Hephaestus\Framework\Hephaestus;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Helpers\Collection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Command\Option as CommandOption;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;
use Hephaestus\Framework\Events\ApplicationChangeMaintenanceMode;
use Illuminate\Contracts\Events\Dispatcher;

class ToggleMaintenanceSlashCommand extends AbstractSlashCommand
{
    /**
     * @inheritdoc
     */
    public string $name = "toggle-maintenance";

    /**
     * @inheritdoc
     */
    public string $description = "Passe ou non en mode maintenance.";

    /**
     * @inheritdoc
     */
    public int $type = Command::CHAT_INPUT;

    // public array $options = [

    // ];


    public function handle(InteractionDTO $interactionDTO): void
    {
        /**
         * @var Hephaestus
         * */
        $hepha = app(Hephaestus::class);
        $previous = app()->isDownForMaintenance();

        config(['hephaestus.maintenance' =>
            !$previous
        ]);
        $now = app()->isDownForMaintenance();

        $prev_mode = $previous ? "ON" : "OFF";
        $mode = $now ? "ON" : "OFF";
        // dump($prev_mode, $mode, app()->isDownForMaintenance());
        app(Dispatcher::class)->dispatch(new ApplicationChangeMaintenanceMode($now, $previous));

        $interactionDTO->messageBuilder->addEmbed(
            new Embed($interactionDTO->discord, [
                "title" => "Don't worry i'm here",
                "description" => "Maintenance mode was `{$prev_mode}` and is now `{$mode}`",
                "color" => 15844367,
                "fields" => new Collection([]),
            ])
        );
    }
}
