<?php

namespace App\InteractionHandlers\SlashCommands;

use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Hephaestus\Framework\Hephaestus;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Helpers\Collection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;

class Test extends AbstractSlashCommand
{
    /**
     * @inheritdoc
     */
    public string $name = "test";

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
        /**
         * @var Hephaestus
         * */
        $hepha = app(Hephaestus::class);

        $commands = $hepha->loader->hydratedHandlers(HandledInteractionType::APPLICATION_COMMAND);
        $commandsCount = $commands->count();
        $strCommands = $commands->map(fn (Command $command) => "- `/{$command->name}` : {$command->description}")->join("\n");

        $interactionDTO->messageBuilder->addEmbed(
            new Embed($hepha->discord, [
                "title" => "Don't worry i'm here",
                "description" => $strCommands,
                "color" => 15844367,
                "fields" => new Collection([]),
            ])
        );
    }
}
