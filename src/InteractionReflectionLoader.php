<?php


namespace Hephaestus\Framework;

use Hephaestus\Framework\Contracts\InteractionHandler;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Abstractions\AbstractInteractionDriver;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\AbstractSlashCommandsDriver;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\Abstractions\MessageComponents\AbstractMessageComponent;
use Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver;
use Hephaestus\Framework\Contracts\BaseInteractionMiddleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;
use ReflectionClass;

/**
 *
 */
class InteractionReflectionLoader
{
    use InteractsWithLoggerProxy;

    public function __construct(
        public HephaestusApplication $hephaestusApplication,
    ) {
    }

    public function make(HephaestusApplication $hephaestusApplication): self
    {
        return new static($hephaestusApplication);
    }

    public function loadAll()
    {
        foreach (HandledInteractionType::cases() as $type) {
            $this->load($type);
        }
    }

    public function load(HandledInteractionType $type, bool $force = false): array
    {
        $key = $this->hephaestusApplication->getCachedInteractionHandlersPath($type);

        $existing = $this->hephaestusApplication->make('cache')->get($key);

        // dd($existing, $key);

        if ($force || is_null($existing)) {
            $classes = $this->getClasses($type);
            Cache::forget($key);
            Cache::forever($key, $classes);

            return $this->load($type, false);
        }
        $this->log("info", "BINDING INTERACTION HANDLERS", [__METHOD__, $existing]);
        // collect($existing)->each(fn ($class) => dd($class));
        foreach($existing as $fullQualifiedClassName) {
            // $this->hephaestusApplication->bind(InteractionHandler::class, fn () => )
            $this->hephaestusApplication->bind($fullQualifiedClassName, $fullQualifiedClassName);

        }


        return collect($existing)
            // ->unique()
            ->toArray();
    }

    public function getClasses(HandledInteractionType $type): array
    {
        /**
         * Key for handlers in :
         * config/hephaestus.php
         */
        $configHandlerTypeKey = Str::of(ucwords(strtolower($type->name), '_'))
            ->replace('_', '')
            ->plural()
            ->toString();
        $workspacePath = $this->resolvePathFor($type);
        $classes = collect([]);
        if (File::isDirectory(app_path())) { // * We're loading an app configuration (checking directories and namespaces)
            $workspaceClasses = $this->extractClasses($type);
            $classes = $classes->concat($workspaceClasses);
        }
        if (File::isDirectory(config_path())) { // * We're loading from a literal configuration (injecting from `config/hephaestus.php` to service container)
            $classes = $classes->concat(config("hephaestus.handlers.{$configHandlerTypeKey}"));
        }

        // dd($classes);
        // * Key to bind from vendor or namespace
        //    dd($configHandlerTypeKey, config("hephaestus.handlers.{$configHandlerTypeKey}"), $workspacePath);
        $this->log("debug", "Before :" . $classes->count(), [__METHOD__]);
        // try {
            // $duplicates = $classes->map(fn ($c) => new $c)
            //     ->duplicates(fn($class) =>$class->getDiscriminator())
            //     ;
            //     dump($duplicates);
            // if($duplicates->count() > 0) {
            //     $message = $duplicates->reduce(fn (InteractionHandler $class) => class_basename($class) . " duplicates {$class->getDiscriminatorAttributeName()} for {$class->getDiscriminator()}\n", "");

            //     $this->log("critical", $message, [__METHOD__]);
            //     // throw new Exception($message);
            // }
        // } catch (Exception $e) {

        //     exit(-1);
        // }
        // $this->log("debug", "After :" . $duplicates->count(), [__METHOD__]);

        return $classes
            ->all();
    }

    public function makePathName(string $string)
    {
        return app_path("InteractionHandlers" . DIRECTORY_SEPARATOR . $string);
    }

    /**
     * Resolve
     */
    public function resolvePathFor(HandledInteractionType $type)
    {
        return match ($type) {
            HandledInteractionType::APPLICATION_COMMAND                 =>  $this->makePathName("SlashCommands"),
            HandledInteractionType::APPLICATION_COMMAND_AUTOCOMPLETE    =>  $this->makePathName("Autocompletes"),
            HandledInteractionType::MESSAGE_COMPONENT                   =>  $this->makePathName("MessageComponents"),
            HandledInteractionType::MODAL_SUBMIT                        =>  $this->makePathName("ModalSubmits"),
            HandledInteractionType::PING                                =>  $this->makePathName("Pings"),
        };
    }

    /**
     *
     */
    public function resolveAbstraction(HandledInteractionType $type): mixed
    {
        return match ($type) {
            HandledInteractionType::APPLICATION_COMMAND                 =>  AbstractSlashCommand::class,
            HandledInteractionType::MESSAGE_COMPONENT                   =>  AbstractMessageComponent::class,
        };
    }

    /**
     * Extract classes from the provided application path.
     */
    public function extractClasses(HandledInteractionType $type): Collection
    {
        $path = $this->resolvePathFor($type);
        // dd(File::allFiles($path), $path);
        $classes = collect(File::allFiles($path))
            ->map(function ($file) {
                $relativePath = str_replace(
                    Str::finish(app_path(), DIRECTORY_SEPARATOR),
                    '',
                    $file->getPathname()
                );

                $folders = Str::beforeLast(
                    $relativePath,
                    DIRECTORY_SEPARATOR
                ) . DIRECTORY_SEPARATOR;

                $className = Str::after($relativePath, $folders);

                $class = app()->getNamespace() . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    $folders . $className
                );

                return $class;
            });
        // ->filter(function ($strClass) use ($type) {
        //     $class = new ReflectionClass($strClass);

        //     return !$class->isAbstract()
        //         && $class->implementsInterface(InteractionHandler::class)
        //         && $class->isSubclassOf($this->resolveAbstraction($type));
        // });
        $resolvedCount = count($classes);
        if (!$classes->count()) {
            // $this->hephaestus->log("Empty path {$path}!", Level::Warning, [$path]);
        }

        // $this->hephaestus->log("Found <fg=cyan>{$resolvedCount}</> classes meting filtering conditions in {$path}");

        return $classes;
    }

    /**
     * Used to resolve handlers classes for a given type
     * @return Collection<string>,
     **/
    public function extractClassesFor(HandledInteractionType $type): Collection
    {
        $pathName = $this->resolvePathFor($type);

        return $this->extractClassesFromPath($pathName)
            ->filter(function ($strClass) use ($type) {
                $class = new ReflectionClass($strClass);

                return !$class->isAbstract()
                    && $class->implementsInterface(InteractionHandler::class)
                    && $class->isSubclassOf($this->resolveAbstraction($type));
            });
    }

    public function extractClassesFromPath(string $pathName): Collection
    {
        $classes = collect()
            ->map(function ($file) {
                $relativePath = str_replace(
                    Str::finish(app_path(), DIRECTORY_SEPARATOR),
                    '',
                    $file->getPathname()
                );

                $folders = Str::beforeLast(
                    $relativePath,
                    DIRECTORY_SEPARATOR
                ) . DIRECTORY_SEPARATOR;

                $className = Str::after($relativePath, $folders);

                $class = app()->getNamespace() . str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    $folders . $className
                );

                return $class;
            });
        $resolvedCount = count($classes);
        if (!$classes->count()) {
            // $this->hephaestus->log("Empty path {$path}!", Level::Warning, [$path]);
        }

        // $this->hephaestus->log("Found <fg=cyan>{$resolvedCount}</> classes meting filtering conditions in {$path}");

        return $classes;
    }

    public function bind(HandledInteractionType $type)
    {
        // $this->hephaestus->log("Binding {$type->name}");
        switch ($type) {
            case HandledInteractionType::APPLICATION_COMMAND:
                /**
                 * @var AbstractSlashCommandsDriver $driver
                 */
                $driver = app(AbstractSlashCommandsDriver::class);
                $driver->register();

                break;
            default:
                // $this->hephaestus->log("Cannot bind {$type->name}. Unimplementend.");
                break;
        }

        $classes = $this->load($type);
    }

    public function bindAll()
    {
        foreach (HandledInteractionType::cases() as $type) {
            $this->bind($type);
        }
        return $this;
    }

    /**
     *
     * @return Collection<InteractionHandler>
     */
    public function hydratedHandlers(HandledInteractionType $type, ?bool $force = false): Collection
    {
        return collect($this->load($type, $force))
            ->map(fn ($class) => app($class)); // Cast into appropriate container service
    }

    public function getDriver(HandledInteractionType $type): AbstractInteractionDriver|null
    {
        return match ($type) {
            HandledInteractionType::APPLICATION_COMMAND => app(AbstractSlashCommandsDriver::class),
            HandledInteractionType::MESSAGE_COMPONENT   => app(MessageComponentsDriver::class),
            default => null,
        };
    }

    public function getMiddlewares(): Collection
    {
        $classes = $this->extractClassesFromPath(
            app_path('InteractionHandlers' . DIRECTORY_SEPARATOR . 'Middlewares')
        );

        $classes = $classes->merge(config('hephaestus.middlewares', null));

        return $classes->filter(function ($strClass) {
            $class = new ReflectionClass($strClass);

            return !$class->isAbstract()
                && $class->isSubclassOf(BaseInteractionMiddleware::class);
        });
    }
}
