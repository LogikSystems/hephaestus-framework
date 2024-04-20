<?php

namespace App\Bot\InteractionHandlers\SlashCommands;

use App\Bot\InteractionHandlers\HandledInteractionType;
use App\Contracts\InteractionHandler;
use Discord\Discord;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;

abstract class AbstractSlashCommand
extends Command
implements InteractionHandler
{

    public function __construct()
    {
        // $attr = get_class_;

        $attributes = array_merge(
            get_class_vars(get_class($this)),
            $this->fillable,
            [
                "type"                          => Command::CHAT_INPUT,
                "description"                   => "An Hephaestus command",
                "default_member_permissions"    => 0,
            ]
        );

        parent::__construct(
            discord: app(Discord::class),
            attributes: $attributes
        );
    }

    /**
     * @inheritdoc
     */
    public string $name;

    public string $description;

    public function handle(Interaction $interaction): void
    {
    }
}
