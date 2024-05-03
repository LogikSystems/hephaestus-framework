<?php

namespace Hephaestus\Framework\Listeners;

use Discord\Discord;
use Discord\Parts\User\Activity;
use Hephaestus\Framework\Events\ApplicationChangeMaintenanceMode;
use Hephaestus\Framework\Hephaestus;
use Hephaestus\Framework\InteractsWithLoggerProxy;

class ApplicationChangeMaintenanceModeListener
{
    use InteractsWithLoggerProxy;

    public function handle(ApplicationChangeMaintenanceMode $event)
    {
        /**
         * @var Discord|null $discord
         */
        $discord = app()->make(Discord::class);

        $this->log("debug", "Application is " . ($event->newValue ? "going into" : "exiting from") . " maintenance mode.", [__METHOD__]);
        // dump(get_class($discord));
        $strWhetherInMaintenance = config("app.name") . " " . ($event->newValue ? "in maintenance" : "working");
        $activity = $discord->getFactory()->create(
            Activity::class,
            [
                'name'      => $strWhetherInMaintenance,
                'type'      => Activity::TYPE_WATCHING,
            ]

        );

        $this->log("info", $inMaintenance = app()->isDownForMaintenance() ? " BOT IS UNDER MAINTENANCE 🟠 " : " BOT IS WORKING 🟢 ");

        $discord->updatePresence($activity, $event->newValue);
    }
}
