<?php

namespace Hephaestus\Framework;

use Closure;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event as DiscordWebsocketEvent;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\AbstractSlashCommandsDriver;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\ISlashCommandsDriver;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\SlashCommandsDriver;
use Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver;
use Hephaestus\Framework\Bootstrap\RegisterInteractionHandlers;
use Hephaestus\Framework\Commands\Components\ConsoleLogRecord;
use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Events\ApplicationChangeMaintenanceMode;
use Hephaestus\Framework\Events\DiscordInteractionEvent;
use Hephaestus\Framework\InteractionReflectionLoader;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Application as LaravelZeroApplication;
use Illuminate\Support\Str;
use LaravelZero\Framework\Providers\GitVersion\GitVersionServiceProvider;
use Symfony\Component\Console\Logger\ConsoleLogger;

use function React\Promise\all;

class HephaestusApplication
extends LaravelZeroApplication
{
    use InteractsWithLoggerProxy;

    static string $INTERACTION_HANDLERS_CACHE_KEY = "APP_INTERACTION_HANDLERS";

    public function __construct(
        string $base_path,
    ) {
        parent::__construct(
            basePath: $base_path
        );

        $this->singleton(HephaestusApplication::class, fn () => $this);

        // dd($this->make(InteractionReflectionLoader::class));
        // $this->booted(fn() => dd($this->make(AbstractSlashCommandsDriver::class)));

    }

    public function isDownForMaintenance(): bool
    {
        return config('hephaestus.maintenance', false);
    }

    public function toggleDownForMaintenance(): void
    {
        $old_value = $this->isDownForMaintenance();
        $this['config']['hephaestus.maintenance'] = !$old_value;
        Event::dispatch(new ApplicationChangeMaintenanceMode(!$old_value));
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
    public function getCachedInteractionHandlersPath(HandledInteractionType $handledInteractionType): string
    {
        $pluralizedTypeName = Str::plural($handledInteractionType->name);

        return $this->normalizeCachePath(
            Str::upper(self::$INTERACTION_HANDLERS_CACHE_KEY . '-' . $pluralizedTypeName),
            Str::lower("cache/hephaestus/{$pluralizedTypeName}.php")
        );
    }

    /**
     *
     */
    public function reloadSlashCommands()
    {
        if (($in_maintenance_on_start = $this->isDownForMaintenance() === false)) {
            $this->toggleDownForMaintenance();
        }
        all(
            $this->make(AbstractSlashCommandsDriver::class)
                ->register(force: true)
        )
            ->then(fn () => $in_maintenance_on_start ? $this->toggleDownForMaintenance() : "");
    }
}
