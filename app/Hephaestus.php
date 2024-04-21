<?php

namespace App;

use App\Commands\Components\ConsoleLogRecord;
use App\Framework\Enums\HandledInteractionType;
use App\Framework\InteractionDispatcher;
use App\Framework\InteractionHandlers\ApplicationCommands\Drivers\SlashCommandsDriver;
use App\Framework\InteractionReflectionLoader;
use Discord\Discord;
use Discord\Parts\Interactions\Interaction;
use Discord\WebSockets\Event;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Psr\Log\LogLevel;
use React\Stream\CompositeStream;
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableResourceStream;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

use function React\Promise\all;

class Hephaestus
{
    // use HasLog;

    /**
     *
     */
    public function __construct(
        public ?OutputInterface $command = null,
        public ?Discord $discord = null,
        public ?string $token = null,
        // public ?array $slashCommands = null,
        // public ?array $interactionHandlers = null,
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
    public static function make(?OutputInterface $output = null): self
    {
        return new static(command: $output);
    }

    public function setOutput(OutputInterface $output)
    {
        $this->command = $output;
    }


    public function beforeConnection(): void
    {
    }

    public function connect(): void
    {
        $this->beforeConnection();

        $this->log("Logging in...");
        // dd(getenv());
        // dd(config('discord'));
        $loggerChannelNameForDiscord = config("discord.logger") ?? "null";
        // dd($loggerChannelNameForDiscord);
        $discordLoggerChannelConfig = config("logging.channels.{$loggerChannelNameForDiscord}");
        $this->discord = new Discord([
            'token'     => $this->getToken(),
            'intents'   => config('discord.intents'),
            'logger'    => Log::build($discordLoggerChannelConfig),
            // 'loop'      => \React\EventLoop\Factory::create(),
        ]);
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
            // $this->cacheInteractionHandlers();
            // $this->loader->bind(HandledInteractionType::APPLICATION_COMMAND);

            $this->registerApplicationSlashCommands();

            // $this->discord->guilds->get("id", 1230346340933042269) #SDA
            //     ->channels
            //     ->get("id", 1230346340933042272)
            //     ->sendMessage(
            //         MessageBuilder::new()
            //             ->setContent("Test")
            //             ->addComponent(
            //                 ActionRow::new()
            //                     ->addComponent(
            //                         Button::new(Button::STYLE_PRIMARY, "azeaze")
            //                             ->setLabel("Test")
            //                     )
            //             )
            //     );
        });

        // Bind our entrypoint
        $this->discord->on(Event::INTERACTION_CREATE, fn (Interaction $interaction) => $this->dispatcher->handle($interaction));

        // $this->discord->getLoop()->addPeriodicTimer(5, function () {
        //     $this->command->writeln("<fg=red>test</>");
        // });
    }

    public function beforeDisconnection(): void
    {
    }

    public function disconnect(): void
    {
        $this->discord->close(true);
        $this->log("Goodbye.");
    }

    public function getToken()
    {
        return $this->token ?? $this->token = config('discord.token');
    }

    public function registerApplicationSlashCommands(): void
    {
        $this->log("Loading application slash commands");
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
        $this->log("Registering stream");
        if (windows_os()) {
            return $this;
        }

        if ($this->outputStream) {
            return $this;
        }

        /**
         * @var Kernel $kernel;
         */
        $kernel = app(Kernel::class);

        // ResourceStream
        // $this->inputStream = new ReadableResourceStream(STDIN, $this->discord->getLoop());

        /**
         * @var StreamOutput $streamOutput
         */
        // $streamOutput = app(StreamOutput::class);
        // $streamOutput->
        // $streamOutput->write()

        $this->discord->getLoop();
        // $this->discord->getLoop()->addWriteStream($streamOutput->getStream(), function ($stream) {
            // dd($stream);
            // fwrite($stream, "<bg=white> Je suis au bon endroit ? </>"); La réponse était non.
        // });
            // $this->discord->getLoop()


        // $streamOutput->writeln("<bg=red> ATTENTION ! </>");
        // dd();

        // $this->outputStream = new WritableResourceStream(STDOUT, $this->discord->getLoop());
        // dd($kernel);

        // $composite = new CompositeStream($this->inputStream, $this->outputStream);

        // $this->outputStream->on('data', fn ($data) => var_dump($data));

        // $this->outputStream->on("drain", function () {
            // $this->command->writeln("Stream is now ready to accept more data");
        // });
        // $this->outputStream->write("test !", []);
        // $this->outputStream->

        // $this->outputStream->on("data", function ($data) {
        // });

        // $this->outputStream->on("data", function($data) {
        //     $this->log($data);
        // });

        return $this;
    }

    // /**
    //  * Load Interactions Handlers into cache
    //  * callback received when a new interaction is created
    //  * @see <\App\Hephaestus::handleDiscordPHPLoop>
    //  * @see <\Discord\WebSockets\Event>
    //  */
    // public function cacheInteractionHandlers(): self
    // {
    //     // InteractionReflectionLoader::load(HandledInteractionType::APPLICATION_COMMAND);
    //     $this->loader->loadAll();
    //     return $this;
    // }


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

        // Log::log($level->name, strip_tags($message), $context);
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
