<?php

use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Formatter\OutputFormatterStyleStack;
use Symfony\Component\Console\Helper\DebugFormatterHelper;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        // 'discord' => [
        //     'driver' => 'custom',
        //     'via' => \Hephaestus\Framework\Logs\DiscordLogger::class,
        //     'suffix' => env('DISCORD_LOG_SUFFIX', 'Laravel Log'), // Message title suffix
        //     'webhook' => env('DISCORD_LOG_WEBHOOK', "https://discord.com/api/webhooks/1234539125281919081/jCqHfcEuOPZjEwK9zkbwTXXmjjSlsw6uhRbq8WTwDgpNIDdyIOlb1F0vNbohwUOsFT4Y"), // e.g. https://discordapp.com/api/webhooks/...
        //     'level' => env('DISCORD_LOG_LEVEL', 'debug'), // You can choose from: emergency, alert, critical, error, warning, notice, info and debug
        //     'context' => env('DISCORD_LOG_CONTEXT', true), // Enable this if you want to receive the full context of an error, usually useless
        //     'environment' => env('DISCORD_LOG_ENVIRONMENT', 'development'), // Enable logging only for environment ['production', 'staging', 'local']
        //     'message' => env('DISCORD_LOG_MESSAGE', false), // Here you can put extra message or tag role or person via @personName
        // ],

        'stack' => [
            'driver' => 'stack',
            'channels' => [
                // "stdout",
                // "daily",
                "single",
            ],
            'ignore_exceptions' => false,
        ],

        'single' => [ # Used for discordphp bot's logging
            'driver' => 'single',
            'path' => storage_path('logs/' . env("APP_NAME", null) . '.log'),
            // 'tap' => [App\Logging\HephaestusFormatter::class],
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/' . env("APP_NAME", null) . '.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            // 'formatter' => App\Logging\HephaestusFormatter::class,
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            // 'tap' => [App\Logging\HephaestusFormatter::class],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'stdout' => [
            'driver'    => 'monolog',
            'level'     => env('LOG_LEVEL', 'debug'),
            'handler'   => StreamHandler::class,
            'formatter' => DebugFormatterHelper::class,
            'with' => [
                'stream' => 'php://stdout',
            ],
        ],
    ],

];
