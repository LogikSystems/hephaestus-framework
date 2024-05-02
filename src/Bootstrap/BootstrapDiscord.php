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
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
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

        $app->singleton(Discord::class, function () use ($app) {

            $discord = new Discord([
                ...$app['config']['discord'],
                "logger" => app(LoggerProxy::class),
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

        // $app->bind(AbstractSlashCommand::class, );



        // $consoleOutputInterface = new ConsoleOutput();
        // dd($consoleOutputInterface);

        /**
         * @var Discord $discord
         */
        $discord = $app->make(Discord::class);
        // $second = new ReadableResourceStream(, $discord->getLoop());



        /**
        //  * @var Buffered
         */

        $readableResourceStream = new ReadableResourceStream(STDIN, $discord->getLoop());
        $writableResourceStream = new WritableResourceStream(STDOUT, $discord->getLoop());
        $app->singleton(
            ReadableResourceStream::class,
            fn () => $readableResourceStream
        );
        $app->singleton(
            WritableResourceStream::class,
            fn () => $writableResourceStream
        );

        // $writableResourceStream->on('drain', function () use ($app, $writableResourceStream) {
        //     dd("drain");
        // });

        $section_haut = $app->make('consoleoutput.section_haut');
        $section_bas = $app->make('consoleoutput.section_bas');
        // Make users/developers able to type in STDIN and
        // interpret this data as it's an Artisan command
        $readableResourceStream->on('data', function ($data) use ($app) {
            if (strlen($data = ($data = trim($data))) <= 1) {
                return -1;
            }

            $artisan = $app->make(Kernel::class);
            try {
                $outputSection = $app->make('consoleoutput.temp');

                $artisan->call($data, [], $outputSection);
            } catch (Exception $e) {
                $app->make(LoggerProxy::class)->log('critical', '<fg=red>'.$e->getMessage().'</>', [__METHOD__, $e]);
            }
        });
    }
}
