<?php

namespace App\Providers;

use App\Framework\InteractionHandlers\AbstractInteractionDriver;
use App\Framework\InteractionHandlers\ApplicationCommands\Drivers\AbstractSlashCommandsDriver;
use App\Framework\InteractionHandlers\ApplicationCommands\Drivers\ISlashCommandsDriver;
use App\Framework\InteractionHandlers\ApplicationCommands\Drivers\SlashCommandsDriver;
use App\Framework\InteractionHandlers\MessageComponents\Drivers\MessageComponentsDriver;
use App\Hephaestus;
use Discord\Interaction;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Reflector;
use Illuminate\Support\ServiceProvider;
use LaravelZero\Framework\Kernel as FrameworkKernel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

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
        $app = $this->app;
        /**
         * @var PackageManifest $packageManifest
         */
        // $packageManifest = $app->make(PackageManifest::class);

        // dd($this->app->getBindings());

        /**
         * @var Kernel $kernel
         */
        // $kernel = app(Kernel::class);
        // $kernel->handle()

        // $a = new FrameworkKernel();

        // $kernel->output()

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


        $this->app->when(Hephaestus::class)
            ->needs(OutputInterface::class)
            ->give(function () {
                return $this->app->make(OutputInterface::class);
            });

        // $this->app->bind(
        //     StreamOutput::class,
        //     fn () => new StreamOutput(STDOUT, StreamOutput::VERBOSITY_NORMAL, true)
        // );

        $this->app->singleton(
            Hephaestus::class,
            fn () => new Hephaestus(
                app(OutputInterface::class)
            )
        );


        // Interaction::verifyKey();


        // * Define preferred Driver for Slash Commands
        $this->app->bind(
            ISlashCommandsDriver::class,
            \App\Framework\InteractionHandlers\ApplicationCommands\Drivers\SlashCommandsDriver::class,
            true
        );

        $this->app->when(
            AbstractInteractionDriver::class
        )->needs(Hephaestus::class)
            ->give(fn () => app(Hephaestus::class));

        // * Define singleton ref to drivers that might be
        // $this->app->when(AbstractSlashCommandsDriver::class)
        //     ->needs(Hephaestus::class)
        //     ->give(fn () => app(Hephaestus::class));

        /**
         * @var Hephaestus
         */
        // $hephaestus = app(Hephaestus::class);
        // $hephaestus->connect();

        // app(GlobalCommandRepository::class)


        $this->app->bind(GlobalCommandRepository::class, fn () => $this->app->make(Hephaestus::class)->discord?->application?->commands);
        // $this->app->when(AbstractSlashCommandsDriver::class)
        //     ->needs('$globalCommandRepository')
        //     ->give(fn () => $this->app->make(GlobalCommandRepository::class));
    }
}
