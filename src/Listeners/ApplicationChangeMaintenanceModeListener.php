<?php

namespace Hephaestus\Framework\Listeners;

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
         * @var Hephaestus|null $hephaestus
         */
        $hephaestus = app()->make(Hephaestus::class);

        $this->log("debug", "Application is " . ($event->newValue ? "going into" : "exiting from") . " maintenance mode.", [__METHOD__]);
        // dump(get_class($hephaestus->discord));
        $strWhetherInMaintenance = config("app.name") . " " . ($event->newValue ? "in maintenance" : "working");
        $activity = $hephaestus->discord->getFactory()->create(
            Activity::class,
            [
                'name'      => $strWhetherInMaintenance,
                'type'      => Activity::TYPE_LISTENING,
            ]

        );
        $hephaestus->discord->updatePresence($activity, $event->newValue);
    }
}
