<?php

namespace Hephaestus\Framework;

use LaravelZero\Framework\Kernel;
use Illuminate\Contracts\Events\Dispatcher;

class HephaestusKernel extends Kernel {

    protected $bootstrappers = [
        \LaravelZero\Framework\Bootstrap\CoreBindings::class,
        \LaravelZero\Framework\Bootstrap\LoadEnvironmentVariables::class,
        \LaravelZero\Framework\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \LaravelZero\Framework\Bootstrap\RegisterFacades::class,
        \LaravelZero\Framework\Bootstrap\RegisterProviders::class,

        \Hephaestus\Framework\Bootstrap\RegisterInteractionHandlers::class,

        \Illuminate\Foundation\Bootstrap\BootProviders::class,

    ];

public function __construct(HephaestusApplication $hephaestusApplication, Dispatcher $dispatcher)
    {
        parent::__construct(
            $hephaestusApplication,
            $dispatcher
        );
    }
}
