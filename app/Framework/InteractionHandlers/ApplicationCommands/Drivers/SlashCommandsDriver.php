<?php

namespace App\Framework\InteractionHandlers\ApplicationCommands\Drivers;

use App\Hephaestus;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Command\Command;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Illuminate\Support\Collection;
use Monolog\Level;
use React\Promise\Promise;

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
    public function register(): void
    {
        $globalCommandRepository = await($this->hephaestus->discord->application->commands->freshen());

        // $this->diffDelete(
        //     $this->getCommandsByName(),
        //     $globalCommandRepository,
        // );

        $this->createOrUpdate(
            $this->getCommandsByName(),
            $globalCommandRepository,
        );
    }

    public function diffDelete(Collection $commandsByName, GlobalCommandRepository $globalCommandRepository)
    {
        $this->hephaestus->log("Checking for GCR Commands... It has <fg=green>" . $globalCommandRepository->count() . "</> commands.");
        foreach ($globalCommandRepository as $gcr_command) {
            $is_present = $commandsByName->has($gcr_command->name);
            $color = $is_present ? "green" : "red";
            $str = $is_present ? "Yes" : "No";
            $this->hephaestus->log("Checking if we have also have the {$gcr_command->name} found on GCR : {$str} <bg={$color}> {$gcr_command->name} </>.");

            $promise = new Promise(fn () => $this->hephaestus->log("Je suis pas perdu."),);

            if (!$is_present) {
                $globalCommandRepository->delete($gcr_command)
                    ->done(onFulfilled: function () use ($gcr_command) {
                        $this->hephaestus->log("<fg=green>Deleted command {$gcr_command->name}</>");
                    }, onRejected: function () use ($gcr_command) {
                        $this->hephaestus->log("<fg=red>>Rejected deletion of command {$gcr_command->name}</>");
                    });
            }
        }
    }

    public function createOrUpdate(Collection $commandsByName, GlobalCommandRepository $globalCommandRepository)
    {
        $promises = [];
        foreach ($commandsByName as $commandName => $command) {
            $promises[] = $promise = $this->updateOne($globalCommandRepository, $commandName, $command);
            $promise->done(
                onFulfilled: fn ()  => $this->hephaestus->log("<fg=green>Added or upddated slash command {$commandName}</>"),
                onRejected: fn ()   => $this->hephaestus->log("<fg=red>Can't add or update slash command {$commandName}</>")
            );
        }

        all($promises)->then(
            onFulfilled: fn () => $this->hephaestus->log("<bg=green> Successed while updating Slash Commands ! </>", Level::Info),
            onRejected: fn () => $this->hephaestus->log("<bg=red> Failed while updating Slash Commands ! </>", Level::Warning),
        );
    }


    function updateOne(GlobalCommandRepository $globalCommandRepository, string $commandName, Command $command)
    {
        $c = new Command(
            $this->hephaestus->discord,
            CommandBuilder::new()
                ->setName($command->name)
                ->setDescription($command->description)
                ->setType(Command::CHAT_INPUT)
                ->toArray()
        );

        return $globalCommandRepository
            ->save($c);
    }
}
