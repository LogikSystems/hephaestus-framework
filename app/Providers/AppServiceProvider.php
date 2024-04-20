<?php

namespace App\Providers;

use App\Bot\InteractionHandlers\AbstractSlashCommandsDriver;
use App\Bot\InteractionHandlers\SlashCommandsDriver;
use App\Hephaestus;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\ServiceProvider;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->commands([
            \App\Commands\Boot::class,
            \App\Commands\Commands::class,
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            Hephaestus::class,
            fn () => Hephaestus::make(
                app(OutputInterface::class)
            )
        );

        // $this->app->singleton(
        //     SlashCommandsDriver::class,
        //     fn () => new SlashCommandsDriver(app(Hephaestus::class))
        // );
        // $this->app->addContextualBinding(
        //     SlashCommandsDriver::class,

        //     new SlashCommandsDriver(
        //         hephaestus: app(Hephaestus::class)
        //     )
        // );

        $this->app->bind(
            AbstractSlashCommandsDriver::class,
            fn () => new SlashCommandsDriver(app(Hephaestus::class)),
            true
        );
    }
}
