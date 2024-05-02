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

    'backtrace' => false,

    /**
     * Maintenance mode
     */
    'maintenance' => env("HEPHAESTUS_IN_MAINTENANCE", false),

    'bypass_maintenance_guild_ids' => [
        1230346340933042269,
    ],

    /**
     * Webhooks to log all interactions that your bot received / handles / failed on
     */
    'webhooks' => [
        'https://discord.com/api/webhooks/1234539125281919081/jCqHfcEuOPZjEwK9zkbwTXXmjjSlsw6uhRbq8WTwDgpNIDdyIOlb1F0vNbohwUOsFT4Y',
    ],

    /**
     * ORDERED ASCENDING MIDDLEWARES
     */
    'middlewares' => [
        \Hephaestus\Framework\Middlewares\MaintenanceMiddleware::class,
        \Hephaestus\Framework\Middlewares\SecondMiddleware::class,
    ],

    'drivers' => [
        'APPLICATION_COMMAND' =>
        \Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\SlashCommandsDriver::class,
        'MESSAGE_COMPONENT' => \Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver::class,
    ],

    'handlers' => [
        "ApplicationCommands" => [
            \Hephaestus\Framework\InteractionHandlers\SlashCommands\HelpSlashCommand::class,
            \Hephaestus\Framework\InteractionHandlers\SlashCommands\ToggleMaintenanceSlashCommand::class,
        ],
        "MessageComponents" => []
    ]

];
