<?php

namespace Hephaestus\Framework;

use Stringable;

trait InteractsWithLoggerProxy {

    public function log($level, string|Stringable $message, array $context = []): void
    {
        app(LoggerProxy::class)->log(...func_get_args());
    }
}
