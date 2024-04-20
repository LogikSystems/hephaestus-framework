<?php

namespace App\InteractionHandlers\SlashCommands;

use App\Framework\Enums\HandledInteractionType;
use App\Framework\InteractionHandlers\ApplicationCommands\AbstractSlashCommand;
use App\Hephaestus;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;

class HelpSlashCommand extends AbstractSlashCommand
{
    /**
     * @inheritdoc
     */
    public string $name = "help";

    /**
     * @inheritdoc
     */
    public string $description = "Affiche l'aide";

    /**
     * @inheritdoc
     */
    public int $type = Command::CHAT_INPUT;

    public function handle(Interaction $interaction): void
    {
        /**
         * @var Hephaestus
         * */
        $hepha = app(Hephaestus::class);

        $commands = $hepha->loader->hydratedHandlers(HandledInteractionType::APPLICATION_COMMAND);
        $commandsCount = $commands->count();
        $strCommands = $commands->map(fn(Command $command) => "\n - `/{$command->name}` : {$command->description}");

        $interaction->respondWithMessage(
            MessageBuilder::new()
                ->addEmbed(new Embed($hepha->discord, [
                    "title" => "Don't worry i'm here",
                    "description" => $strCommands,
                    "color" => 15844367,
                    "fields" => new Collection([]),
                ])
                )
        );
    }
}
