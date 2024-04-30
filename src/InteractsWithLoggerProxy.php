<?php

namespace Hephaestus\Framework;

use Stringable;

trait InteractsWithLoggerProxy
{

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $organizedContext = [
            'backtrace'         => config('hephaestus.backtrace', false) ? debug_backtrace() : null,
            'dev_log_context'   => array_key_exists(0, $context) && is_string($context[0])  ? $context[0] : null,
            'other_context'     => is_string($context) ? $context : [array_shift($context)]
        ];

        app(LoggerProxy::class)->log('debug', $message, $organizedContext);
    }
}
