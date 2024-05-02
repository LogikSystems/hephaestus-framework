<?php

namespace Hephaestus\Framework\Bootstrap;

use Exception;
use Illuminate\Console\BufferedConsoleOutput;
use Illuminate\Testing\ParallelConsoleOutput;
use Laravel\Prompts\Output\BufferedConsoleOutput as OutputBufferedConsoleOutput;
use Laravel\Prompts\Output\ConsoleOutput;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Contracts\BootstrapperContract;
use React\EventLoop\LoopInterface;
use React\Stream\WritableResourceStream;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\ProgressBar;
use Termwind\HtmlRenderer;
use Termwind\Termwind;

class BootstrapConsoleOutput implements BootstrapperContract
{

    /**
     * @param Application|HephaestusApplication $app
     */
    public function bootstrap(Application $app): void
    {
        if (!$app instanceof \Hephaestus\Framework\HephaestusApplication) {
            throw new Exception("Cannot bootstrap a non Hephaestus Application.");
        }



         /**
         * @var ConsoleOutput The console output
         */
        $bufferedConsoleOutput = app(BufferedConsoleOutput::class);

        $app->singleton(
            'consoleoutput', fn () => $bufferedConsoleOutput,
        );

        $section_haut = $bufferedConsoleOutput->section();
        $section_haut->setMaxHeight(1);

        $temp = $bufferedConsoleOutput->section();
        $temp->setMaxHeight(10);


        $section_bas = $bufferedConsoleOutput->section();
        $section_bas->setMaxHeight(5);

        $app->singleton(
            'consoleoutput.temp', fn () => $temp,
        );

        // die;
        $app->singleton(
            'consoleoutput.section_haut', fn () => $section_haut,
        );

        $progress = new ProgressBar($section_haut);
        $progress->setMessage(" Connecting bot... ");
        $progress->setBarCharacter('<fg=red>❤</>');
        $progress->setEmptyBarCharacter('<fg=red>♡</>');
        $progress->start(1);

        // $progress->

        $app->singleton('consoleoutput.section_haut.progressbar', fn () => $progress);

        // $section_haut->("<fg=red> BOT IS STARTING... </>");
        $app->singleton(
            'consoleoutput.section_bas', fn () => $section_bas,
        );

        // $section_input = $bufferedConsoleOutput->section();
        // $section_input->setMaxHeight(1);

        // $stdio = new \Clue\React\Stdio\Stdio(
        //     loop: null,
        //     input: null,
        //     output: null,
        //     readline: null,
        // );

        // $app->singleton('app.stdio', $stdio);
        // $stdio->addInput("> ");
        // die;
        // $bufferedConsoleOutput->
    }
}
