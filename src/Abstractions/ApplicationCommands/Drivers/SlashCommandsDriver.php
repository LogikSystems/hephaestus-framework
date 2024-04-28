<?php

namespace Hephaestus\Framework\Abstractions\ApplicationCommands\Drivers;

use Hephaestus\Framework\Hephaestus;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Command\Command;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Exception;
use Illuminate\Support\Collection;
use Monolog\Level;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;

use function React\Async\async;
use function React\Async\await;
use function React\Promise\all;

/**
 * Driver
 */
class SlashCommandsDriver extends AbstractSlashCommandsDriver
{
    /**
     * @inheritdoc
     */
    public function register(): array
    {
        $promises = [];
        $globalCommandRepository = await($this->hephaestus->discord->application->commands->freshen());
        // $this->hephaestus->discord->getLoop()->addPeriodicTimer(0, fn () => $this->hephaestus->log("cc"));

        $promises[] = $this->diffDelete(
            $this->getCommandsByName(),
            $globalCommandRepository,
        )
            ->then(
                onFulfilled: fn () => $this->hephaestus->log("info", "<bg=green> Successed while updating Global Commands Repository ! </>", [__METHOD__]),
                onRejected: fn () => $this->hephaestus->log("warning", "<bg=red> Failed while updating Global Commands Repository ! </>", [__METHOD__]),
            );

        $promises[] = $this->createOrUpdate(
            commandsByName: $this->getCommandsByName(),
            globalCommandRepository: $globalCommandRepository,
        )
            ->then(
                onFulfilled: fn () => $this->hephaestus->log("info", "<bg=green> Successed while updating Slash Commands ! </>", [__METHOD__]),
                onRejected: fn () => $this->hephaestus->log("warning", "<bg=red> Failed while updating Slash Commands ! </>", [__METHOD__]),
            );

        return $promises;
    }

    public function diffDelete(Collection $commandsByName, GlobalCommandRepository $globalCommandRepository)
    {
        $promises = [];
        $this->hephaestus->log("debug", "Checking for GCR Commands... It has <fg=green>" . $globalCommandRepository->count() . "</> commands.", [__METHOD__]);
        foreach ($globalCommandRepository as $gcr_command) {
            $is_present = $commandsByName->has($gcr_command->name);
            $color = $is_present ? "green" : "red";
            $str = $is_present ? "Yes" : "No";
            $this->hephaestus->log("debug", "Checking if we have also have the {$gcr_command->name} found on GCR : {$str} <bg={$color}> {$gcr_command->name} </>.", [__METHOD__]);

            // $promise = new Promise(fn () => $this->hephaestus->log("Je suis pas perdu."),);

            if (!$is_present) {
                $promises[] = $promise = $globalCommandRepository->delete($gcr_command);
                $promise->done(
                    onFulfilled: fn () => $this->hephaestus->log("info", "Deleted command {$gcr_command->name}", [__METHOD__]),
                    onRejected: fn () => $this->hephaestus->log("warning", "Rejected deletion of command {$gcr_command->name}", [__METHOD__])
                );
            }
        }

        return all($promises);
    }

    public function createOrUpdate(Collection $commandsByName, GlobalCommandRepository $globalCommandRepository)
    {
        $promises = [];
        foreach ($commandsByName as $commandName => $command) {
            $promises[] = $promise = $this->updateOne($globalCommandRepository, $commandName, $command);
            $promise->done(
                onFulfilled: fn ()  => $this->hephaestus->log("debug", "Added or upddated slash command {$commandName}", [__METHOD__]),
                onRejected: fn ()   => $this->hephaestus->log("warning", "Can't add or update slash command {$commandName}", [__METHOD__])
            );
        }
        return all($promises);
    }

    /**
     * Update a command
     *
     */
    function updateOne(GlobalCommandRepository $globalCommandRepository, string $commandName, Command $command) : ExtendedPromiseInterface
    {
        return $globalCommandRepository
            ->save($command);
    }
}
