<?php

namespace Hephaestus\Framework\Abstractions\MessageComponents;

use Hephaestus\Framework\Contracts\InteractionHandler;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\InteractsWithLoggerProxy;

abstract class AbstractMessageComponent implements InteractionHandler
{

    use InteractsWithLoggerProxy;

    public string $component_custom_id;

    /**
     * @inheritdoc
     */
    public function getDiscriminatorAttributeName(): string
    {
        return 'component_custom_id';
    }

    /**
     * @inheritdoc
     */
    public function getDiscriminator(): string
    {
        return $this[$this->getDiscriminatorAttributeName()];
    }
}
