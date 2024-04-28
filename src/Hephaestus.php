<?php

namespace Hephaestus\Framework;

use Hephaestus\Framework\Commands\Components\ConsoleLogRecord;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\InteractionDispatcher;
use Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\SlashCommandsDriver;
use Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver;
use Hephaestus\Framework\InteractionReflectionLoader;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Hephaestus\Framework\Events\DiscordInteractionEvent;
use Hephaestus\Framework\Listeners\DiscordInteractionEventListener;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Event as FacadesEvent;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerTrait;
use Psr\Log\LogLevel;
use React\EventLoop\LoopInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableResourceStream;
use React\Stream\WritableStreamInterface;
use Stringable;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

use function React\Async\await;
use function React\Promise\all;

class Hephaestus
{
    use InteractsWithLoggerProxy;



    public function __construct(
        public ?OutputInterface $command = null,
        public ?string $token = null,
        public ?Discord $discord = null,
        // public ?array $slashCommands = null,
        // public ?array $interactionHandlers = null,
        public ?ReadableStreamInterface $inputStream = null,
        public ?WritableStreamInterface $outputStream = null,
        public ?InteractionReflectionLoader $loader = null,
        // public ?InteractionDispatcher $dispatcher = null,
        public ?LoopInterface $loopInterface = null,
        public ?HephaestusApplication $hephaestusApplication = null
    ) {
        // $this->dispatcher = new InteractionDispatcher($this);
        $this->loader = new InteractionReflectionLoader(app());

        $this->hephaestusApplication = app();

        // $this->loopInterface = new EvLoop
    }

    /**
     * Create a new instances
     * Used for IoC (?)
     */
    public static function make(
        ?OutputInterface $output = null
    ): self {
        return new self();
    }


    public function beforeConnection(): void
    {
    }

    public function connect(): void
    {
        $this->log("info", "Logging in...", [__METHOD__]);

        $loggerChannelNameForDiscord = config("discord.logger") ?? "null";

        $discordLoggerChannelConfig = config("logging.channels.{$loggerChannelNameForDiscord}");
        $this->discord = new Discord([
            'token'         => $this->getToken(),
            // 'description'   => config('discord.description'),
            'intents'       => config('discord.intents'),
            'logger'        => Log::build($discordLoggerChannelConfig),
            // 'loop'      => \React\EventLoop\Factory::create(),
        ]);
        $this->log("info", "Logged in.", [__METHOD__]);

        $this->registerStream();
        // var_dump(self::getContainerCacheKey(), Cache::get(join(".", [self::getContainerCacheKey(),"APPLICATION_COMMAND"])));
        // Bind listeners to reactphp discordphp loop events
        $this->handleDiscordPHPLoop();
    }

    /**
     *
     */
    public function handleDiscordPHPLoop()
    {
        // $this->discord->application->commands->create(new CommandCommand($this->discord))
        $this->discord->on('ready', function (...$args) {
            /**
             * @var MessageComponentsDriver $msg
             */
            // $msg = app(MessageComponentsDriver::class);
            // dd($msg->getRelatedHandlers()->first());

            //register events here
            $this->log("info", "<bg=cyan> DiscordPHP is ready </>");
            // $this->cacheInteractionHandlers();
            // $this->loader->bind(HandledInteractionType::APPLICATION_COMMAND);

            all($this->registerApplicationSlashCommands())
                ->then(function () {
                    // * Bind our entrypoint
                    $this->discord->on(Event::INTERACTION_CREATE, function (Interaction $interaction, Discord $discord) {
                        $this->log("info", "Dispatching event through Laravel event dispatcher");
                        dump(get_class($discord), get_class($interaction));
                        $event = new DiscordInteractionEvent($interaction, $discord);
                        FacadesEvent::dispatch($event);
                        $this->log("info", "Dispatched event through Laravel event dispatcher");
                    });
                });
        });
    }

    public function beforeDisconnection(): void
    {
    }

    public function disconnect(): void
    {
        $this->discord->close(true);
        $this->log("info", "Goodbye.");
    }

    public function getToken()
    {
        return $this->token ?? $this->token = config('discord.token');
    }

    public function registerApplicationSlashCommands(): void
    {
        $this->log("info", "Reloading application slash commands");
        /**
         * @var SlashCommandsDriver $slashCommands
         */
        $slashCommands = app(SlashCommandsDriver::class);
        $slashCommands->register();
    }

    /**
     * Register the input and output streams.
     */
    public function registerStream(): self
    {
        $this->log("info", "Registering stream");
        if (windows_os()) {
            return $this;
        }

        // ResourceStream
        $this->inputStream = new ReadableResourceStream(STDIN, $this->discord->getLoop());
        $this->outputStream = new WritableResourceStream(STDOUT, $this->discord->getLoop());
        // $this->discord->getLoop();

        return $this;
    }

    /**
     * Send a message to the console.
     */
    // public function log(string $message, LogLevel|Level|null $level = Level::Debug, ?array $context = []): void
    // {
    //     if ($level instanceof string) {
    //         $level = Level::fromName($level);
    //     }

    //     $message = trim($message);
    //     Log::log($level->name, strip_tags($message), $context);
    //     // dd(env('APP_VERBOSITY'));
    //     if ($level->value >= Level::fromName(config('app.verbosity_level', 'debug'))->value) {
    //         if (empty($message)) {
    //             return;
    //         }

    //         $color = match ($level->toPsrLogLevel()) {
    //             LogLevel::EMERGENCY, LogLevel::CRITICAL => "red",
    //             LogLevel::ALERT, LogLevel::WARNING      => "yellow",
    //             LogLevel::INFO                          => "green",
    //             LogLevel::DEBUG                         => "blue",
    //             default                                 => "white",
    //         };

    //         $timestamp = config('discord.timestamp', "Y-m-d H:i:s");


    //         $config = [
    //             'bgColor'   => $color,
    //             'fgColor'   => 'white',
    //             'level'     => $level->name,
    //             'timestamp' => $timestamp ? now()->format($timestamp) : null,
    //         ];
    //         //
    //         with(new ConsoleLogRecord($this->command))->render($config, $message);
    //     }
    // }

    public static function getHandlerCacheKey(HandledInteractionType $type)
    {
        return join(".", [
            self::getContainerCacheKey(),
            $type->name,
        ]);
    }

    public static function getContainerCacheKey()
    {
        return config("app.name", "HEPHAESTUS-FRAMEWORK");
    }


}
