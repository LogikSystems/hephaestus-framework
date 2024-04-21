<?php

namespace App\Framework\InteractionHandlers\ApplicationCommands;

use App\Contracts\InteractionHandler;
use App\Hephaestus;
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
        parent::__construct(app(Hephaestus::class)->discord, $attributes);
    }

    /**
     * @inheritdoc
     */
    public string $name;

    /**
     * @inheritdoc
     */
    public string $description;

    /**
     * @inheritdoc
     */
    public abstract function handle(Interaction $interaction): void;
}
