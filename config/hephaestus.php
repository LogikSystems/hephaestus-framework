<?php

return [

    'drivers' => [
        'APPLICATION_COMMAND' => \Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers\SlashCommandsDriver::class,
        'MESSAGE_COMPONENT' => \Hephaestus\Framework\Abstractions\MessageComponents\Drivers\MessageComponentsDriver::class,
    ],

    'handlers' => [
        "ApplicationCommands" => [
            \Hephaestus\Framework\InteractionHandlers\SlashCommands\HelpSlashCommand::class
        ],
        "MessageComponents" => [

        ]
    ]

];
