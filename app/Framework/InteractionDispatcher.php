<?php

namespace App\Framework;

use App\Hephaestus;
use App\Contracts\InteractionHandler;
use App\Framework\Enums\HandledInteractionType;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Illuminate\Support\Facades\Cache;
use Monolog\Level;

class InteractionDispatcher implements InteractionHandler
{
    public function __construct(
        public Hephaestus $hephaestus,
    ) {
        // $this->loader = new InteractionReflectionLoader($this->hephaestus);
        // $this->hephaestus->command->log("Creating an " . __METHOD__);
        // $this->hephaestus->log("<bg=blue> TEST </>");
    }

    /**
     * @inheritdoc
     */
    public function handle(Interaction $interaction): void
    {
        $this->hephaestus->log(__METHOD__ . ' receiving interaction', Level::Debug, [$interaction]);
        $handledType = HandledInteractionType::from($interaction->type);
        $this->hephaestus->log(__METHOD__ . " interaction is <fg=cyan>{$handledType->name}</>", Level::Debug, [$interaction]);

        //
        $this->dispatch($handledType, $interaction);
        // Handling unresponded interactions
        if (!$interaction->isResponded()) {
            $interaction->respondWithMessage(
                MessageBuilder::new()
                    ->setContent("Il y'a un problème avec cette interaction.")
            );
        }
    }

    /**
     *
     */
    public function dispatch(HandledInteractionType $type, Interaction $interaction)
    {
        $forThisTypeHandlers = Cache::get(Hephaestus::getHandlerCacheKey($type));
        // $this->hephaestus->log(__METHOD__ . " For <fg=cyan>{$type->name}</>." . " Cache has :<fg=cyan>" . count($forThisTypeHandlers) ?? 0 . "</> handlers.");

        $driver = $this->hephaestus->loader->getDriver($type);
        if(!$driver) {
            $this->hephaestus->log("<bg=red> No driver for {$type->name} </>", Level::Warning,  []);
        } else {
            $this->hephaestus->log("Resolved driver", Level::Debug, [$driver]);
            $interactionHandler = $driver->find($interaction);
            if(!$interactionHandler) {
                $this->hephaestus->log("No handler found for {$type->name}", Level::Info,  []);
            } else {
                $this->hephaestus->log("Resolved interaction handler", Level::Debug, [$interactionHandler]);
                $interactionHandler->handle($interaction);
            }
        }
    }
}
