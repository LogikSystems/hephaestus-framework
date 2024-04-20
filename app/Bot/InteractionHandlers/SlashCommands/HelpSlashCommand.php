<?php

namespace App\Bot\InteractionHandlers\SlashCommands;

use App\Bot\InteractionHandlers\SlashCommands\AbstractSlashCommand;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

class HelpSlashCommand extends AbstractSlashCommand
{
    public string $name = "test";

    public function handle(Interaction $interaction): void
    {
        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->setContent("Oulalala")
        );
    }
}
