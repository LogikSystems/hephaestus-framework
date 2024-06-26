<?php

namespace Hephaestus\Framework\Commands\Components;

use Illuminate\Console\Contracts\NewLineAware;
use Illuminate\Console\View\Components\Component;
use Illuminate\Console\View\Components\Mutators;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Support\Str;

use function Termwind\render;
use function Termwind\renderUsing;

class ConsoleLogRecord extends Component
{
    /**
     * Renders the component using the given arguments.
     *
     * @param  array  $style
     * @param  string  $string
     * @param  int  $verbosity
     * @return void
     */
    public function render($style, $string, $verbosity = OutputInterface::VERBOSITY_NORMAL)
    {
        // $this->output->writeln($string);
        // return;
        $string = $this->mutate($string, [
            Mutators\EnsureDynamicContentIsHighlighted::class,
            Mutators\EnsureRelativePaths::class,
        ]);

        $mutatedStringContext = $this->mutate($style['context'], [
            Mutators\EnsureRelativePaths::class,
        ]);
        return $this->renderView('log', array_merge($style, [
            'marginTop' => $this->output instanceof NewLineAware ? max(0, 2 - $this->output->newLinesWritten()) : 1,
            'content'   => $string,
            'appName'   => config('app.name', 'hephaestus-framework'),
            'context'   => $mutatedStringContext
        ]), $verbosity);
    }

    /**
     * Renders the given view.
     *
     * @param  string  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  int  $verbosity
     * @return string
     */
    protected function renderView($view, $data, $verbosity) : string
    {
        renderUsing($this->output);

        return (string) $this->compile($view, $data);
    }

    /**
     * Compile the given view contents.
     *
     * @param  string  $view
     * @param  array  $data
     * @return void
     */
    public function compile($view, $data)
    {
        extract($data);

        $path = resource_path("/views/components/{$view}.php");

        ob_start();

        include $path;

        return tap(ob_get_contents(), function () {
            ob_end_clean();
        });
    }
}
