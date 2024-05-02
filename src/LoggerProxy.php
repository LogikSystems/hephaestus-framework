<?php

namespace Hephaestus\Framework;

use Carbon\Carbon;
use Hephaestus\Framework\Commands\Components\ConsoleLogRecord;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Monolog\Level;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerProxy implements LoggerInterface
{
    public function __construct()
    {
    }

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
        $logLevel = Level::fromName($level);
        $minimumLevelForStdout = Level::fromName(config('app.verbosity_level', 'debug'));

        $output = $this->getOutput();
        // $data = $this->buffered->fetch();
        $logger = $this->getLogger();

        if (!is_null($logger)) {
            $logger->log($level, strip_tags($message), $context['other_context'] ?? []);
        }
        if (
            !is_null($output) &&
            $logLevel->value >= $minimumLevelForStdout->value
        ) {
            $this->getOutput()->writeln(
                (strlen($message =
                    trim(
                        Arr::join([
                            Carbon::now()->format(config('discord.timestamp', 'd-m-Y H:i:s')),
                            $level,
                            trim(strip_tags($message)),
                            PHP_EOL
                        ], " ")
                    ))
                    > 150
                    ? substr($message, 0, 146) . "..." . PHP_EOL
                    : $message)
            );
            // $output->write($this->buffered->fetch());
            // $color = match ($logLevel->toPsrLogLevel()) {
            //     LogLevel::EMERGENCY, LogLevel::CRITICAL => "red",
            //     LogLevel::ALERT, LogLevel::WARNING      => "yellow",
            //     LogLevel::INFO                          => "green",
            //     LogLevel::DEBUG                         => "blue",
            //     default                                 => "white",
            // };

            // $timestamp = config('discord.timestamp', "Y-m-d H:i:s");
            // // dd($context['backtrace'][1]);
            // $config = [
            //     'maintenance'   => app()->isDownForMaintenance(),
            //     'context'       => $context['dev_log_context'] ?? null,
            //     'backtraces'    => $context['backtrace'] ?? null,
            //     'bgColor'       => $color,
            //     'fgColor'       => 'white',
            //     'level'         => $logLevel->name,
            //     'timestamp'     => $timestamp ? now()->format($timestamp) : null,
            // ];
            // $html = with(new ConsoleLogRecord($output))->render($config, $message);
        }
    }

    public function getOutput(): OutputInterface|null
    {
        return app('consoleoutput.section_bas');
    }
    public function getLogger(): LoggerInterface|null
    {
        return app(Logger::class);
    }
}
