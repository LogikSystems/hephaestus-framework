<?php

namespace App\Bot\InteractionHandlers\SlashCommands;

use App\Bot\InteractionHandlers\HandledInteractionType;
use App\Bot\InteractionHandlers\SlashCommands\AbstractSlashCommand;
use App\Hephaestus;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;

class ZetaSlashCommand extends AbstractSlashCommand
{
    /**
     * @inheritdoc
     */
    public string $name = "test";

    /**
     * @inheritdoc
     */
    public string $description = "Affiche l'aide";

    public int $type = Command::CHAT_INPUT;

    public function handle(Interaction $interaction): void
    {

    }
}
