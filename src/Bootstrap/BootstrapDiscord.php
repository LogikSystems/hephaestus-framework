<?php

namespace Hephaestus\Framework\Bootstrap;

use Discord\Discord;
use Discord\WebSockets\Event;
use Exception;
use Hephaestus\Framework\Abstractions\ApplicationCommands\AbstractSlashCommand;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\HephaestusKernel;
use Hephaestus\Framework\LoggerProxy;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\PackageManifest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Laravel\Prompts\Output\BufferedConsoleOutput;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use Monolog\Handler\StreamHandler;
use React\Stream\DuplexResourceStream;
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Style\OutputStyle;

class BootstrapDiscord implements BootstrapperContract
{


    /**
     * @param Application|HephaestusApplication $app
     */
    public function bootstrap(Application $app): void
    {
        if (!$app instanceof \Hephaestus\Framework\HephaestusApplication) {
            throw new Exception("Cannot bootstrap a non Hephaestus Application.");
        }
        $stream = fopen(storage_path('logs/discord.php.log'), 'w');
        $app->singleton(Discord::class, function () use ($app, $stream) {

            $discord = new Discord([
                ...$app['config']['discord'],
                "logger" => Log::build([
                    "driver"    => "monolog",
                    "handler"   => StreamHandler::class,
                    "with" => [
                        "stream" => $stream,
                    ]
                ]),
            ]);

            return $discord;
        });

        $app->singleton('hephaestus.framework.version', function () use ($app) {
            $filePossibleLocation = with($app->make(
                PackageManifest::class
            ))->vendorPath . DIRECTORY_SEPARATOR . 'logiksystems/hephaestus-framework' . DIRECTORY_SEPARATOR . 'composer.json';
            // * If we're in an logiksystems/`hephaestus-application` skeleton `logiksystems/hephaestus-framework`:
            if (File::isDirectory($filePossibleLocation)) {
                return json_decode(File::get($filePossibleLocation))['version'] ?? app('git.version');
            }
            // * If we're in package
            return false;
        });

        $app->afterResolving(Discord::class, fn () => app(LoggerProxy::class)->log('info', 'Resolving discord'));

        /**
         * @var Discord $discord
         */
        $discord = $app->make(Discord::class);
        /**
         * @var BufferedConsoleOutput
         */
        $console = app('consoleoutput');
        // dump($console->getStream());
        $loop = $discord->getLoop();
        $readableResourceStream = new ReadableResourceStream(STDIN, $loop);
        $writableResourceStream = new WritableResourceStream(STDOUT, $loop);
        // $readableResourceStream = new ReadableResourceStream(fopen(storage_path('logs/discord.php.log'), 'r'), $discord->getLoop());
        $readableResourceStream->pipe($writableResourceStream, []);
        $stdio = new \Clue\React\Stdio\Stdio(
            loop: $loop,
            input: $readableResourceStream,
            output: $writableResourceStream,
            readline: null,
        );
        $stdio->setPrompt("> ");

        $app->singleton('app.stdio', fn () => $stdio);


        $section_haut = $app->make('consoleoutput.section_haut');
        $section_bas = $app->make('consoleoutput.section_bas');

        $stdio->on('data', function ($data) use ($app, &$stdio, $writableResourceStream) {

            if (strlen($data = ($data = trim($data))) <= 1) {
                return -1;
            }

            $artisan = $app->make(Kernel::class);
            try {
                // $outputSection = $app->make('consoleoutput.temp');

                $artisan->call($data, [], $app->make('consoleoutput.section_bas'));
            } catch (Exception $e) {
                $app->make(LoggerProxy::class)->log('critical', '<fg=red>' . $e->getMessage() . '</>', [__METHOD__, $e]);
            }
        });
    }
}
