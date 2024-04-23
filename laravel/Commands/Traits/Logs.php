<?php

namespace App\Commands\Traits;

use App\Commands\Components\ConsoleLogRecord;
use Illuminate\Support\Facades\Log;
use Monolog\Level;
use Psr\Log\LogLevel;

trait Logs
{
    /**
     * Send a message to the console.
     */
    public function log(string $message, string|Level|null $type = "debug", ?array $context = []): void
    {
        if(($type instanceof Level)) {
            $type = $type->toPsrLogLevel();
        }
        $message = trim($message);

        if (empty($message)) {
            return;
        }

        $color = match ($type) {
            LogLevel::EMERGENCY, LogLevel::CRITICAL => "red",
            LogLevel::ALERT, LogLevel::WARNING      => "yellow",
            LogLevel::INFO                          => "green",
            LogLevel::DEBUG                         => "blue",
            default                                 => "white",
        };

        $timestamp = config('discord.timestamp', "Y-m-d H:i:s");

        $config = [
            'bgColor' => $color,
            'fgColor' => 'white',
            'title' => $type,
            'timestamp' => $timestamp ? now()->format($timestamp) : null,
        ];

        with(new ConsoleLogRecord($this->getOutput()))->render($config, $message);
        Log::log($type, $message, $context);
    }

    /**
     * Send a warning log to console.
     *
     * @param  string  $string
     * @param  string|null  $verbosity
     * @return void
     */
    public function warn($string)
    {
        return $this->log($string, 'warn');
    }

    /**
     * Send an error log to console.
     *
     * @param  string  $string
     * @param  string|null  $verbosity
     * @return void
     */
    public function error($string)
    {
        return $this->log($string, 'error');
    }
}
