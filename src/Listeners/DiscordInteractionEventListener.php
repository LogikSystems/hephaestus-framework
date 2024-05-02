<?php

namespace Hephaestus\Framework\Listeners;

use Closure;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection as DiscordCollection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\Contracts\BaseInteractionMiddleware;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Events\DiscordInteractionEvent;
use Hephaestus\Framework\Hephaestus;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\InteractionReflectionLoader;
use Hephaestus\Framework\InteractsWithLoggerProxy;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Process\Pipe;
use Monolog\Level;
use Symfony\Component\Console\Output\OutputInterface;

class DiscordInteractionEventListener
{
    use InteractsWithLoggerProxy;

    public function __construct(
        public Hephaestus $hephaestus,
        public HephaestusApplication $hephaestusApplication,
        public InteractionReflectionLoader $interactionReflectionLoader
    ) {
    }

    public function handle(DiscordInteractionEvent $event)
    {
        /**
         * @var OutputInterface $output
         */
        $output = app(OutputInterface::class);

        $this->log("info", "Received event", [__METHOD__]);

        $dto = new InteractionDTO($event->interaction->data, $event->discord);
        $pipeline = new Pipeline($this->hephaestusApplication);

        $handledType = $event->getType();
        $acknowledgeable = [
            HandledInteractionType::APPLICATION_COMMAND,
            HandledInteractionType::MESSAGE_COMPONENT,
            HandledInteractionType::MODAL_SUBMIT
        ];

        if (!in_array($handledType, $acknowledgeable)) {
            return $event->interaction->user->sendMessage(MessageBuilder::new()->setContent("Désolé je peux pas encore"));
        }

        $handler = $this->hephaestusApplication
            ->make(InteractionReflectionLoader::class)
            ->getDriver($handledType)
            ->find($event->interaction);
        $middlewares = $this->interactionReflectionLoader
            ->getMiddlewares()
            ->toArray();

        $pipeline
            ->setContainer($this->hephaestusApplication)
            ->send($dto)
            ->pipe(...$middlewares)
            ->via('handle')
            ->then(fn (InteractionDTO $_dto) => $handler->handle($_dto));

        $event->interaction->respondWithMessage($dto->messageBuilder);
    }
}
