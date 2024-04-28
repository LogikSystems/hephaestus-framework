<?php

namespace Hephaestus\Framework\Commands;

use Discord\Parts\Interactions\Interaction;
use HelpCommand;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\Hephaestus;
use Hephaestus\Framework\InteractionHandlers\SlashCommands\HelpSlashCommand;
use Hephaestus\Framework\Models\HelpCommandModel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Signals;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BootCommand extends Command
{

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
    public function handle(HephaestusApplication $hephaestusApplication, Hephaestus $hephaestus)
    {
        // /**
        //  * @var OutputInterface $output
        //  */
        // $output = app(OutputInterface::class);

        // die;

        // dd($hephaestusApplication);

        // $this->output;
        // dd($this->app->getBindings());
        // $this->app->singleton(
        //     Hephaestus::class,
        //     fn () => Hephaestus::make($this->output)
        // );
        /**
         * @var Hephaestus
         */
        // $hephaestus = app(Hephaestus::class);
        // $hephaestus->setOutput($this->output);

        // $hephaestus->log("Starting bot.", Level::Info);
        $hephaestus->connect();

        // dd($hephaestusApplication->isBooted());

        // dd(new HelpCommandModel());

    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
