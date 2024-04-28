<?php

namespace Hephaestus\Framework;

use Closure;
use Discord\Discord;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\SlashCommandsDriver;
use Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver;
use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\InteractionReflectionLoader;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Application as LaravelZeroApplication;
use Illuminate\Support\Str;

class HephaestusApplication
extends LaravelZeroApplication
{

    static string $INTERACTION_HANDLERS_CACHE_KEY = "APP_INTERACTION_HANDLERS";

    public function __construct(
        string $base_path,
        public ?InteractionReflectionLoader $interactionReflectionLoader = null,
        public ?SlashCommandsDriver $slashCommandsDriver = null,
        public ?MessageComponentsDriver $messageComponentsDriver = null,
    ) {
        parent::__construct(
            basePath: $base_path
        );

        if(is_null($interactionReflectionLoader)) {
            $this->interactionReflectionLoader = new InteractionReflectionLoader($this);
        }
    }
    public function isDownForMaintenance(): bool
    {
        return config('hephaestus.maintenance', false);
    }
    /**
     *
     * @return string
     */
    public function getCachedInteractionHandlersPaths(): Collection
    {
        return collect(HandledInteractionType::cases())
            ->map(fn (HandledInteractionType $type) => $this->getCachedInteractionHandlersPath($type));
    }

    /**
     *
     */
    public function getCachedInteractionHandlersPath(HandledInteractionType $handledInteractionType) : string
    {
        $pluralizedTypeName = Str::plural($handledInteractionType->name);

        return $this->normalizeCachePath(
            Str::upper(self::$INTERACTION_HANDLERS_CACHE_KEY . '-' . $pluralizedTypeName),
            Str::lower("cache/hephaestus/{$pluralizedTypeName}.php")
        );
    }

    public function reloadSlashCommands()
    {
        $this->slashCommandsDriver->register();
    }

}
