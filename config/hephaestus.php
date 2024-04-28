<?php

/**
 * __    __  ________  _______   __    __   ______   ________   ______  ________  __    __   ______
 *|  \  |  \|        \|       \ |  \  |  \ /      \ |        \ /      \|        \|  \  |  \ /      \
 *| ██  | ██| ████████| ███████\| ██  | ██|  ██████\| ████████|  ██████\\████████| ██  | ██|  ██████\
 *| ██__| ██| ██__    | ██__/ ██| ██__| ██| ██__| ██| ██__    | ██___\██  | ██   | ██  | ██| ██___\██
 *| ██    ██| ██  \   | ██    ██| ██    ██| ██    ██| ██  \    \██    \   | ██   | ██  | ██ \██    \
 *| ████████| █████   | ███████ | ████████| ████████| █████    _\██████\  | ██   | ██  | ██ _\██████\
 *| ██  | ██| ██_____ | ██      | ██  | ██| ██  | ██| ██_____ |  \__| ██  | ██   | ██__/ ██|  \__| ██
 *| ██  | ██| ██     \| ██      | ██  | ██| ██  | ██| ██     \ \██    ██  | ██    \██    ██ \██    ██
 * \██   \██ \████████ \██       \██   \██ \██   \██ \████████  \██████    \██     \██████   \██████
 *  ______    ______   __    __  ________  ______   ______
 * /      \  /      \ |  \  |  \|        \|      \ /      \
 *|  ██████\|  ██████\| ██\ | ██| ████████ \██████|  ██████\
 *| ██   \██| ██  | ██| ███\| ██| ██__      | ██  | ██ __\██
 *| ██      | ██  | ██| ████\ ██| ██  \     | ██  | ██|    \
 *| ██   __ | ██  | ██| ██\██ ██| █████     | ██  | ██ \████
 *| ██__/  \| ██__/ ██| ██ \████| ██       _| ██_ | ██__| ██
 * \██    ██ \██    ██| ██  \███| ██      |   ██ \ \██    ██
 *  \██████   \██████  \██   \██ \██       \██████  \██████
 */
return [

    /**
     * Maintenance mode
     */
    'maintenance' => env("HEPHAESTUS_IN_MAINTENANCE", false),

    'bypass_maintenance_guild_ids' => [
        // 1230346340933042269,
    ],

    'middlewares' => [
        \Hephaestus\Framework\Middlewares\MaintenanceMiddleware::class,
    ],

    'drivers' => [
        'APPLICATION_COMMAND' => \Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\SlashCommandsDriver::class,
        'MESSAGE_COMPONENT' => \Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver::class,
    ],

    'handlers' => [
        "ApplicationCommands" => [
            \Hephaestus\Framework\InteractionHandlers\SlashCommands\HelpSlashCommand::class
        ],
        "MessageComponents" => []
    ]

];
