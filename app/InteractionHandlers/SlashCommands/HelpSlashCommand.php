<?php

namespace App\InteractionHandlers\SlashCommands;

use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Hephaestus\Framework\Hephaestus;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;

class HelpSlashCommand extends AbstractSlashCommand
{
    /**
     * @inheritdoc
     */
    public string $name = "autrehelpmaiscestpourledebug";

    /**
     * @inheritdoc
     */
    public string $description = "Affiche l'aide";

    /**
     * @inheritdoc
     */
    public int $type = Command::CHAT_INPUT;

    public function handle(InteractionDTO $interactionDTO): void
    {
        // * FAKE COMMAND
    }
}
