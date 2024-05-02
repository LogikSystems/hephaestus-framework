<?php

namespace Hephaestus\Framework\Bootstrap;

use Discord\Discord;
use Exception;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\LoggerProxy;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use Symfony\Component\Console\Output\BufferedOutput;

class BootstrapLoggerProxy implements BootstrapperContract {


    /**
     * @param Application|HephaestusApplication $app
     */
    public function bootstrap(Application $app): void
    {
        if(!$app instanceof \Hephaestus\Framework\HephaestusApplication) {
            throw new Exception("Cannot bootstrap a non Hephaestus Application.");
        }
        $app->singleton(LoggerProxy::class, fn () => new LoggerProxy(new BufferedOutput()));
    }
}
