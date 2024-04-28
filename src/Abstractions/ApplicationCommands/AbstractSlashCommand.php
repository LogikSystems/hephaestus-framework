<?php

namespace Hephaestus\Framework\Abstractions\ApplicationCommands;

use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Hephaestus;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;

abstract class AbstractSlashCommand
extends Command
implements InteractionHandler
{
    use InteractsWithLoggerProxy;



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
        $name = class_basename($this);
        $this->log("debug", "Constructing <fg=cyan>{$name}</>", [__METHOD__]);
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminatorAttributeName(): string
    {
        return 'name';
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminator(): string
    {
        return $this[$this->getDiscriminatorAttributeName()];
    }

    /**
     * @inheritdoc
     */
    public string $name;

    /**
     * @inheritdoc
     */
    public string $description;
}
