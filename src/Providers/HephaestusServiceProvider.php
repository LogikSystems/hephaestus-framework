<?php

namespace Hephaestus\Framework\Providers;

use App\Commands\ClearLogs;
use Illuminate\Support\ServiceProvider;

class HephaestusServiceProvider extends ServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register(): void
    {
        $this->publishes([
            __DIR__ . '/../config/discord.php'
        ], 'hephaestus-discord-config');

        $this->publishes([
            __DIR__ . '/../docker-compose.yml',
            __DIR__ . 'Dockerfile',
            __DIR__ . 'hephaestus-startcontainer.sh',
            __DIR__ . 'hephaestus-buildcontainer.sh',
        ], 'hephaestus-docker-files');
    }

    /**
     *  @inheritdoc
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/discord.php', 'hephaestus-discord-config');

        $this->commands(ClearLogs::class);
    }
}
