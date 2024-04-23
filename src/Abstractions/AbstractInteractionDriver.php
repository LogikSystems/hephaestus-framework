<?php

namespace Hephaestus\Framework\Abstractions;

use Hephaestus\Framework\Contracts\InteractionDriver;
use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Hephaestus;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Collection;

abstract class AbstractInteractionDriver implements InteractionDriver{

    public function __construct(
        public ?Hephaestus $hephaestus = null,
    ) {
        if(is_null($hephaestus)) {
            $this->hephaestus = app(Hephaestus::class);
        }
        // $this->hephaestus->log("Driver created : <fg=cyan>" . $this::class . "</>");
        // dd($this->hephaestus);
    }

    /**
     * @inheritdoc
     */
    public abstract function getHandledInteractionType(): HandledInteractionType;

    /**
     *
     * @return Collection<InteractionHandler|null>
     */
    public function getRelatedHandlers() : Collection|null
    {
        return $this->hephaestus->loader->hydratedHandlers($this->getHandledInteractionType());
    }

    /**
     * @inheritdoc
     */
    public abstract function find(Interaction $interaction): InteractionHandler|null;
}
