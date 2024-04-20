<?php

namespace App\Commands;

use App\Bot\InteractionHandlers\InteractionReflectionLoader;
use App\Bot\InteractionHandlers\SlashCommandsDriver;
use App\Commands\Traits\Logs;
use App\Hephaestus;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Console\Signals;
use Illuminate\Support\Facades\Log;
use LaravelZero\Framework\Commands\Command;
use Monolog\Level;

class Boot extends Command
{

    // use Logs;

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
    public function handle()
    {
        Log::info("APP IS: ", [$this->app]);

        // $this->output;


        $this->app->singleton(
            Hephaestus::class,
            fn () => Hephaestus::make($this->output)
        );




        /**
         * @var Hephaestus
         */
        $hephaestus = $this->app->make(Hephaestus::class);
        $slashCommandLoader = app()->singleton(
            SlashCommandsDriver::class,
            fn () => new SlashCommandsDriver($hephaestus)
        );
        $hephaestus->log("Starting bot.", Level::Info);
        $hephaestus->connect();


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
