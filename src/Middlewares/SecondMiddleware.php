<?php

namespace Hephaestus\Framework\Middlewares;

use Hephaestus\Framework\Contracts\BaseInteractionMiddleware;
use Closure;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;
use Hephaestus\Framework\Hephaestus;

class SecondMiddleware extends BaseInteractionMiddleware
{
    public function handle(InteractionDTO $interactionDTO, Closure $next): mixed
    {
        $state = $interactionDTO->interaction->guild_id > 0;

        return $state ? $next($interactionDTO): null;
    }
}
