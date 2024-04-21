<?php

namespace App\InteractionHandlers\SlashCommands;

use App\Framework\InteractionHandlers\ApplicationCommands\AbstractSlashCommand;
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
    public string $name = "ougachaga";

    /**
     * @inheritdoc
     */
    public string $description = "Ne fait absolument rien.";

    /**
     * @inheritdoc
     */
    public int $type = Command::CHAT_INPUT;

    public function handle(Interaction $interaction): void
    {

    }
}
