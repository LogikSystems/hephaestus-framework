<?php

namespace Hephaestus\Framework\Abstractions;

use Hephaestus\Framework\Contracts\InteractionDriver;
use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Hephaestus;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\InteractionReflectionLoader;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Illuminate\Support\Collection;

abstract class AbstractInteractionDriver implements InteractionDriver
{

    use InteractsWithLoggerProxy;

    public function __construct()
    {

    }

    /**
     * @inheritdoc
     */
    public abstract function getHandledInteractionType(): HandledInteractionType;

    /**
     *
     * @return Collection<InteractionHandler|null>
     */
    public function getRelatedHandlers(): Collection|null
    {
        $this->log("info", "Calling getRelatedHandler", [__METHOD__]);
        return app(InteractionReflectionLoader::class)
            ->hydratedHandlers($this->getHandledInteractionType());
    }

    /**
     * @inheritdoc
     */
    public abstract function find(Interaction $interaction): InteractionHandler|null;
}
