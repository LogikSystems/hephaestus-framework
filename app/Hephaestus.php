<?php

namespace App;

use App\ADiscordBot;
use App\Bot\InteractionHandlers\HandledInteractions;
use App\Bot\InteractionHandlers\HandledInteractionType;
use App\Bot\InteractionHandlers\InteractionDispatcher;
use App\Bot\InteractionHandlers\InteractionReflectionLoader;
use App\Commands\Boot;
use App\Commands\Components\ConsoleLogRecord;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\WebSockets\Event;
use Illuminate\Console\OutputStyle;
use Illuminate\Log\Logger;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use Monolog\Level;
use Monolog\LogRecord;
use Psr\Log\LogLevel;
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableResourceStream;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

use function React\Promise\all;

class Hephaestus extends ADiscordBot
{
    // use HasLog;

    /**
     *
     */
    public function __construct(
        public OutputInterface $command,
        public ?Discord $discord = null,
        public ?string $token = null,
        public ?array $slashCommands = null,
        public ?array $interactionHandlers = null,
        public ?ReadableStreamInterface $inputStream = null,
        public ?WritableStreamInterface $outputStream = null,
        public ?InteractionReflectionLoader $loader = null,
        public ?InteractionDispatcher $dispatcher = null,
    ) {
        $this->dispatcher = new InteractionDispatcher($this);
        $this->loader = new InteractionReflectionLoader($this);
    }

    /**
     * Create a new instances
     * Used for IoC (?)
     */
    public static function make(OutputInterface $output): self
    {
        return new static(command: $output);
    }

    /**
     * @inheritdoc
     */
    public function getSlashCommandsPath(): string
    {

        return app_path("Bot" . DIRECTORY_SEPARATOR . "InteractionHandlers" . DIRECTORY_SEPARATOR . "SlashCommands");
    }

    /**
     * @inheritdoc
     */

    public function getInteractionHandlersPath(): string
    {
        return app_path("Bot" . DIRECTORY_SEPARATOR . "InteractionHandlers" . DIRECTORY_SEPARATOR . "InteractionHandlers");
    }

    public function beforeConnection(): void
    {
        $this->cacheInteractionHandlers();
    }

    public function connect(): void
    {
        $this->beforeConnection();

        $this->log("Logging in...");

        app()->singleton(
            Discord::class,
            fn() => new Discord([
                'token'     => $this->getToken(),
                'intents'   => config('discord.intents'),
                'logger'    => $this->outputStream,
            ])
        );

        $this->discord = app(Discord::class);
        $this->log("Logged in.");
        $this->log("Now sharing Discord as singleton in app container.");

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
        $this->discord->on('ready', function () {
            //register events here
            $this->log("<bg=cyan> DiscordPHP is ready </>", Level::Info);
            $this->loader->bind(HandledInteractionType::APPLICATION_COMMAND);
            $this->discord->guilds->get("id", 1230346340933042269) #SDA
                ->channels
                ->get("id", 1230346340933042272)
                ->sendMessage(
                    MessageBuilder::new()
                        ->setContent("Test")
                        ->addComponent(
                            ActionRow::new()
                                ->addComponent(
                                    Button::new(Button::STYLE_PRIMARY, "azeaze")
                                        ->setLabel("Test")
                                )
                        )
                );
        });

        $this->discord->on('mention', function ($new, $thiis, $old) {
            var_dump($new, $thiis, $old);
        });

        $this->discord->on('reconnected', function () {
        });

        // Bind our entrypoint
        $this->discord->on(Event::INTERACTION_CREATE, fn ($interaction) => $this->dispatcher->handle($interaction));
    }

    public function beforeDisconnection(): void
    {
    }

    public function disconnect(): void
    {
        $this->log("Leaving...");
        $this->discord->close(true);
        $this->log("Goodbye.");
    }

    public function getToken()
    {
        return $this->token ?? $this->token = config('discord.token');
    }

    /**
     * @inheritdoc
     */
    public function getSlashCommands(): array
    {
        if ($this->slashCommands) {
            return $this->slashCommands;
        }
        $slashCommands = $this->loader->load(HandledInteractionType::APPLICATION_COMMAND);

        return $this->slashCommands = $slashCommands;
    }

    /**
     * Register the input and output streams.
     */
    public function registerStream(): self
    {
        $this->log("Registering stream");
        if (windows_os()) {
            return $this;
        }

        if ($this->outputStream) {
            return $this;
        }

        $this->inputStream = new ReadableResourceStream(STDIN, $this->discord->getLoop());


        return $this;
    }

    /**
     * Load Interactions Handlers into cache
     * callback received when a new interaction is created
     * @see <\App\Hephaestus::handleDiscordPHPLoop>
     * @see <\Discord\WebSockets\Event>
     */
    public function cacheInteractionHandlers(): self
    {
        // InteractionReflectionLoader::load(HandledInteractionType::APPLICATION_COMMAND);
        $this->loader->loadAll();
        return $this;
    }


    /**
     * Send a message to the console.
     */
    public function log(string $message, LogLevel|Level|null $level = Level::Debug, ?array $context = []): void
    {
        if ($level instanceof string) {
            $level = Level::fromName($level);
        }

        $message = trim($message);

        if ($level->value >= Level::fromName(config('app.verbosity'))->value) {


            if (empty($message)) {
                return;
            }

            $color = match ($level->toPsrLogLevel()) {
                LogLevel::EMERGENCY, LogLevel::CRITICAL => "red",
                LogLevel::ALERT, LogLevel::WARNING      => "yellow",
                LogLevel::INFO                          => "green",
                LogLevel::DEBUG                         => "blue",
                default                                 => "white",
            };

            $timestamp = config('discord.timestamp', "Y-m-d H:i:s");

            $config = [
                'bgColor'   => $color,
                'fgColor'   => 'white',
                'level'     => $level->name,
                'timestamp' => $timestamp ? now()->format($timestamp) : null,
            ];
            //
            with(new ConsoleLogRecord($this->command))->render($config, $message);
        }

        Log::log($level->name, strip_tags($message), $context);
    }

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
