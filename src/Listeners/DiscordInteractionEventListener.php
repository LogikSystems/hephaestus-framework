<?php

namespace Hephaestus\Framework\Listeners;

use Closure;
use Discord\Builders\MessageBuilder;
use Discord\Helpers\Collection as DiscordCollection;
use Discord\Parts\Embed\Embed;
use Discord\Parts\Interactions\Interaction;
use Hephaestus\Framework\DataTransferObjects\InteractionDTO;
use Hephaestus\Framework\Enums\HandledInteractionType;
use Hephaestus\Framework\Events\DiscordInteractionEvent;
use Hephaestus\Framework\Hephaestus;
use Hephaestus\Framework\HephaestusApplication;
use Hephaestus\Framework\InteractionReflectionLoader;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Process\Pipe;
use Monolog\Level;
use Symfony\Component\Console\Output\OutputInterface;

class DiscordInteractionEventListener
{

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

        $this->hephaestus->log("Received event", Level::Info);


        // ? Speculations :
        // $DTO = [
        //     'interaction'   => $event->interaction,
        //     'response'      => MessageBuilder::new(),
        // ];
        // $pipeline->send($DTO)->then();
        //

        $dto = new InteractionDTO($event->interaction->data, $event->discord);
        $pipeline = new Pipeline($this->hephaestusApplication);


        // ? Expectations :
        // if(is_null($handler)){
        //     return "toz";
        // }
        // if not :
        // $middlewareAndHandler = [];
        // pipeMiddlewareAndHandlers
        //

        $handledType = $event->getType();
        $acknowledgeable = [
            HandledInteractionType::APPLICATION_COMMAND,
            HandledInteractionType::MESSAGE_COMPONENT,
            HandledInteractionType::MODAL_SUBMIT
        ];

        if(!in_array($handledType, $acknowledgeable)) {
            return $event->interaction->user->sendMessage(MessageBuilder::new()->setContent("Désolé je peux pas encore"));
        }

        $handler = $this->hephaestus->loader->getDriver($handledType)->find($event->interaction);

        $extractedMessageBuilder = $pipeline
            ->send($dto)
            ->pipe(
                ...$this->interactionReflectionLoader->getMiddlewares()
            )
            ->then(
                function (InteractionDTO $interactionDTO) use ($event, $handler) {
                    $this->hephaestus->log("Appropriate handler if resolved  called, or not failed,  for {$event->getType()->name}:{$event->interaction->id}");
                    $handler->handle($interactionDTO);
                }
            );
        $event->interaction->respondWithMessage($dto->messageBuilder);
    }
}
