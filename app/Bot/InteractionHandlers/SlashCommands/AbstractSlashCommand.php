<?php

namespace App\Bot\InteractionHandlers\SlashCommands;

use App\Bot\InteractionHandlers\HandledInteractionType;
use App\Contracts\InteractionHandler;
use App\Hephaestus;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;

abstract class AbstractSlashCommand
extends Command
implements InteractionHandler
{

    public function __construct()
    {
        $attributes = array_merge(
            [
                "type"                          => Command::CHAT_INPUT,
                "description"                   => "An Hephaestus command",
                "default_member_permissions"    => 0,
            ],
            get_class_vars($this::class),
        );
    }

    /**
     * @inheritdoc
     */
    public string $name;

    public string $description;

    public abstract function handle(Interaction $interaction): void;
}
