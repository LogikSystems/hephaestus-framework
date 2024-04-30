<?php

namespace Hephaestus\Framework\Abstractions\ApplicationCommands;

use Discord\Discord;
use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Hephaestus;
use Discord\Parts\Interactions\Command\Command;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;

use function React\Async\await;

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
        parent::__construct(app(Discord::class), $attributes);
        $name = class_basename($this);
        $this->log("debug", "Constructing <fg=cyan>{$this->name}</>", [__METHOD__]);
    }

    public function __destruct()
    {
        $this->log("debug", "Destructing <fg=cyan>{$this->name}</>", [__METHOD__]);
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
