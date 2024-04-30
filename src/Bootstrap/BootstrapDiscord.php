<?php

namespace Hephaestus\Framework\Bootstrap;

use Discord\Discord;
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
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;
use React\Stream\WritableResourceStream;

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
                "logger" => new LoggerProxy(),
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

        /**
         * @var Discord $discord
         */
        $discord = $app->make(Discord::class);

        $app->singleton(
            ReadableResourceStream::class,
            fn () => new ReadableResourceStream(STDIN, $app->make(Discord::class)->getLoop())
        );
        $app->singleton(
            WritableResourceStream::class,
            fn () => new WritableResourceStream(STDOUT, $app->make(Discord::class)->getLoop())
        );
        $app->make(ReadableResourceStream::class)->on('data', function ($data) use ($app) {
            $app->make(WritableResourceStream::class)->write('> ');
            if(strlen($data) <= 1) {
                return;
            }
            /**
             * @var Artisan $artisan
             */
            $artisan = $app->make(Kernel::class);

            try {
                $artisan->call($data);

            } catch (Exception $e) {
                $app->make(LoggerProxy::class)->log('critical', $e->getMessage(), [__METHOD__, $e]);
            }
        });
    }
}
