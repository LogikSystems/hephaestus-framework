<?php

use Discord\WebSockets\Intents;

return [

    "token"         => env("DISCORD_BOT_TOKEN", null),

    "description"   => env("DISCORD_BOT_DESCRIPTION", "A bot made using HEPHAESTUS framework's."),

    "intents"       => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT | Intents::GUILD_MEMBERS,

    "options"           => [
        "loadAllMembers" => true,
    ],

    "logger" =>  env('DISCORD_LOGGING_CHANNEL'),

];
