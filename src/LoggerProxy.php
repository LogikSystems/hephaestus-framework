<?php

namespace Hephaestus\Framework;

use Hephaestus\Framework\Commands\Components\ConsoleLogRecord;
use Illuminate\Log\Logger;
use Illuminate\Support\Str;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerProxy implements LoggerInterface
{
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('emergency', $message, $context);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('alert', $message, $context);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('critical', $message, $context);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('error', $message, $context);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('warning', $message, $context);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('notice', $message, $context);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('info', $message, $context);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->writeLog('debug', $message, $context);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->writeLog($level, $message, $context);
    }

    private function writeLog($level, $message, $context)
    {
        // __METHOD__
        $method_name = collect($context)
            ->filter(fn ($value) => is_string($value))
            ->first(function (string $value) {
                return count(explode("::", $value)) == 2;
            });
        $logLevel = Level::fromName($level);
        $minimumLevelForStdout = Level::fromName(config('app.verbosity_level', 'debug'));

        $output = $this->getOutput();
        $logger = $this->getLogger();
        if (!is_null($logger)) {
            $logger->log($level, strip_tags($message), $context);
        }
        if (
            !is_null($output) &&
            $logLevel->value >= $minimumLevelForStdout->value
        ) {

            $color = match ($logLevel->toPsrLogLevel()) {
                LogLevel::EMERGENCY, LogLevel::CRITICAL => "red",
                LogLevel::ALERT, LogLevel::WARNING      => "yellow",
                LogLevel::INFO                          => "green",
                LogLevel::DEBUG                         => "blue",
                default                                 => "white",
            };

            $timestamp = config('discord.timestamp', "Y-m-d H:i:s");

            $config = [
                'maintenance'   => app()->isDownForMaintenance(),
                'context'       => $method_name,
                'bgColor'       => $color,
                'fgColor'       => 'white',
                'level'         => $logLevel->name,
                'timestamp'     => $timestamp ? now()->format($timestamp) : null,
            ];
            with(new ConsoleLogRecord($output))->render($config, $message);
        }
    }

    public function getOutput(): OutputInterface|null
    {
        return app(OutputInterface::class);
    }
    public function getLogger(): LoggerInterface|null
    {
        return app(Logger::class);
    }
}
