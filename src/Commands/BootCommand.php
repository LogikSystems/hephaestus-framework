<?php

namespace Hephaestus\Framework\Commands;

use Discord\Discord;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use LaravelZero\Framework\Commands\Command;

use function React\Promise\all;

class BootCommand extends Command
{

    use InteractsWithLoggerProxy;
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bot:boot';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Starts the bot';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Discord $discord)
    {
        // $this->log("info", "Logging discord", [__METHOD__]);

        // $discord->getLoop()->addPeriodicTimer(5, $callback);
    }
}
