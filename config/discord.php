<?php

use Discord\WebSockets\Intents;
use Hephaestus\Framework\LoggerProxy;

return [

    "token"         => env("DISCORD_BOT_TOKEN", null),

    "intents"       => Intents::getDefaultIntents() | Intents::MESSAGE_CONTENT | Intents::GUILD_MEMBERS,

];
