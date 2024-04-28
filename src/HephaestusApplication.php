<?php

namespace Hephaestus\Framework;

use Closure;
use Discord\Discord;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\SlashCommandsDriver;
use Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver;
use Hephaestus\Framework\Bootstrap\RegisterInteractionHandlers;
use Hephaestus\Framework\Commands\Components\ConsoleLogRecord;
use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\InteractionReflectionLoader;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;
use LaravelZero\Framework\Application as LaravelZeroApplication;
use Illuminate\Support\Str;
use Symfony\Component\Console\Logger\ConsoleLogger;

class HephaestusApplication
extends LaravelZeroApplication
{

    static string $INTERACTION_HANDLERS_CACHE_KEY = "APP_INTERACTION_HANDLERS";

    public function __construct(
        string $base_path,
    ) {
        parent::__construct(
            basePath: $base_path
        );

        $this->afterBootstrapping(RegisterInteractionHandlers::class, function() {
            $this->singleton(InteractionReflectionLoader::class, fn () => new InteractionReflectionLoader($this));

            $this->singleton(LoggerProxy::class, fn() => new LoggerProxy());

            // $this->make(Hephaestus::class);
        });
    }

    public function __destruct()
    {
        $this->make(LoggerProxy::class)->log("critical", "HephaestusApplication destructor called", [__METHOD__]);
    }

    public function isDownForMaintenance(): bool
    {
        // dump("App is down for maintenance ?", config('hephaestus.maintenance'));
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
     * @return string
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
