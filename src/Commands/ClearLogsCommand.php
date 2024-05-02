<?php

namespace Hephaestus\Framework\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

class ClearLogsCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Removes log files.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->writeln("<fg=green>Logs cleared !</>");
        // Log::info("Clearing log files. Goodbye.");
        exec(
            implode(
                " ",
                ["rm", "-f", storage_path("logs/*.log")]
            )
        );
        // Log::info("Logs have been cleared.");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {

    }
}
