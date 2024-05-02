<?php

namespace Hephaestus\Framework\Commands;

use Hephaestus\Framework\HephaestusApplication;
use LaravelZero\Framework\Commands\Command;

class RegisterCommandsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'bot:reload';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Reload (register new, updates, or deletes lost commands) commands to Discord API';

    public function handle(HephaestusApplication $hephaestusApplication)
    {
        $hephaestusApplication->reloadSlashCommands();
    }
}
