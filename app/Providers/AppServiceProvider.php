<?php

namespace App\Providers;

use App\Framework\InteractionHandlers\ApplicationCommands\Drivers\AbstractSlashCommandsDriver;
use App\Framework\InteractionHandlers\ApplicationCommands\Drivers\SlashCommandsDriver;
use App\Hephaestus;
use Illuminate\Support\ServiceProvider;
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
