<?php

namespace App\Framework\InteractionHandlers\ApplicationCommands\Drivers;

use App\Hephaestus;
use Discord\Builders\CommandBuilder;
use Discord\Parts\Interactions\Command\Command;
use Discord\Repository\Interaction\GlobalCommandRepository;
use Exception;
use Illuminate\Support\Collection;
use Monolog\Level;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
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
    public function register(): void
    {
        $promises = [];

        $globalCommandRepository = await($this->hephaestus->discord->application->commands->freshen());
        // $this->hephaestus->discord->getLoop()->addPeriodicTimer(0, fn () => $this->hephaestus->log("cc"));

        $promises[] = $this->diffDelete(
            $this->getCommandsByName(),
            $globalCommandRepository,
        )
            ->then(
                onFulfilled: fn () => $this->hephaestus->log("<bg=green> Successed while updating Global Commands Repository ! </>", Level::Info),
                onRejected: fn () => $this->hephaestus->log("<bg=red> Failed while updating Global Commands Repository ! </>", Level::Warning),
            );

        $promises[] = $this->createOrUpdate(
            commandsByName: $this->getCommandsByName(),
            globalCommandRepository: $globalCommandRepository,
        )
            ->then(
                onFulfilled: fn () => $this->hephaestus->log("<bg=green> Successed while updating Slash Commands ! </>", Level::Info),
                onRejected: fn () => $this->hephaestus->log("<bg=red> Failed while updating Slash Commands ! </>", Level::Warning),
            );


        // all($promises)
        //     ->then(
        //         onFulfilled: fn () => $this->hephaestus->log("<bg=green> Successed while updating registry ! </>", Level::Info),
        //         onRejected: fn () => $this->hephaestus->log("<bg=red> Failed while updating registry ! </>", Level::Warning),
        //     );


        // all($this->sleep())
        //     ->then(
        //         onFulfilled: fn () => $this->hephaestus->log("<bg=green> Successed while updating registry ! </>", Level::Info),
        //         onRejected: fn () => $this->hephaestus->log("<bg=red> Failed while updating registry ! </>", Level::Warning),
        //     );
    }

    public function sleep()
    {
        $_p = [];
        for ($i = 0; $i < 10; $i++) {
            $_p[] = $p = new Promise(
                resolver: function (callable $resolve, callable $reject) {
                    // async(fn () => sleep(10));

                    $time = rand(0, 10);
                    $this->hephaestus->command->writeln("sleeping <fg=green>{$time}</>s");
                    // sleep($time);
                    async(function () use ($time, $resolve) {
                        sleep($time);
                        $resolve($time);
                    });
                    $this->hephaestus->command->writeln("finished");
                    // $deferred = new Deferred($reject);
                    // $deferred->resolve("a");
                    // $resolve();
                },
                canceller: function () {
                    throw new Exception("Promise cancelled !");
                }
            );
        }
        return $_p;
    }

    public function diffDelete(Collection $commandsByName, GlobalCommandRepository $globalCommandRepository)
    {
        $promises = [];
        $this->hephaestus->log("Checking for GCR Commands... It has <fg=green>" . $globalCommandRepository->count() . "</> commands.");
        foreach ($globalCommandRepository as $gcr_command) {
            $is_present = $commandsByName->has($gcr_command->name);
            $color = $is_present ? "green" : "red";
            $str = $is_present ? "Yes" : "No";
            $this->hephaestus->log("Checking if we have also have the {$gcr_command->name} found on GCR : {$str} <bg={$color}> {$gcr_command->name} </>.");

            // $promise = new Promise(fn () => $this->hephaestus->log("Je suis pas perdu."),);

            if (!$is_present) {
                $promises[] = $promise = $globalCommandRepository->delete($gcr_command);
                $promise->done(
                    onFulfilled: fn () => $this->hephaestus->log("<fg=green>Deleted command {$gcr_command->name}</>"),
                    onRejected: fn () => $this->hephaestus->log("<fg=red>>Rejected deletion of command {$gcr_command->name}</>")
                );
            }
        }

        return all($promises);
    }

    public function checkOne()
    {
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
        return all($promises);
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
