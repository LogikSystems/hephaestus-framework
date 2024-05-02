<section>
    <div class="bg-black text-white">
        <hr class="text-<?php echo $bgColor ?>">
        <span class="mx-2 text-<?php echo ($maintenance ? "yellow" : "green") ?>">
            <strong>
                <?php
                echo htmlspecialchars((!$maintenance ? "<=========}---o" : "o---{=========>"));
                ?>
            </strong>
        </span>
        <span>
            <?php
            if (isset($level)) {
            ?>
                <span class="bg-<?php echo $bgColor ?> text-black">
                    <?php echo \Illuminate\Support\Str::of(
                        \Illuminate\Support\Str::wrap(\Illuminate\Support\Str::of(" " . strtoupper($level) . " ")->padBoth(11, '█'), "<span class='mx-1'>", "</span>")
                    )
                    ?>
                </span>
            <?php } ?>
        </span>
        <?php if (isset($timestamp)) { ?>
            |
            <span><?php echo "{$timestamp}" ?></span>
        <?php } ?>
        <?php if (isset($appName)) { ?>
            |
            <span class="bg-<?php echo $maintenance ? 'yellow' : 'green'; ?> text-black">
                <?php echo \Illuminate\Support\Str::of(
                    \Illuminate\Support\Str::wrap(\Illuminate\Support\Str::of($maintenance ? " Maintenance ON " : " Maintenance OFF ")->padBoth("21", '█'), "<span class='mx-1'>", "</span>")
                )  ?></span>
            |
            <?php
            /**
             * Asserting we're in "installation context" of a mocked app (framework not in vendor/ !)
             * skeleton (inside fork from `hephaestus-framework` for example)
             */
            if (app('hephaestus.framework.version')) { ?>
                <span>
                    <?php echo strtoupper(
                        "<span class='text-green'><strong><u>Application</u></strong></span>: " .
                            "<span class='text-green'>{$appName}</span>" . " " . app('git.version')
                    )
                    ?>
                </span>
                |
            <?php
            }
            ?>
            <span>
                <?php echo strtoupper(
                    "<span class='text-brightcyan'><strong><u>Framework</u></strong></span>: " .
                        (!app('hephaestus.framework.version')
                            ? app('git.version')
                            : app('hephaestus.framework.version'))
                ) ?></span>
        <?php } ?>

        <span class="mx-2 text-<?php echo ($maintenance ? "yellow" : "green") ?>">
            <strong>
                <?php
                echo htmlspecialchars((!$maintenance ? "o---{=========>" : "<=========}---o"));
                ?>
            </strong>
        </span>
        <hr class="text-<?php echo $bgColor ?>">
    </div>
    <div>
        <ul class="m-0 pb-1">
            <?php if (!is_null($backtraces)) { ?>
                <li>
                    <div class="text-green mr-1">
                        <b>
                            <u>BACKTRACE :</u>
                        </b>
                    </div>
                    <div class="mr-1">
                        <ol>
                            <?php
                            foreach ($backtraces as $backtrace) {
                            ?>
                                <li>
                                    <?php
                                    if (array_key_exists('file', $backtrace)) {
                                        echo htmlspecialchars('<fg=bright-cyan>' . $backtrace['file'] . '</>' . '<fg=white>' . ' (' . '</>');
                                    }
                                    ?>
                                    <?php
                                    if (array_key_exists('line', $backtrace)) {
                                        echo htmlspecialchars('<fg=white>' . 'line: ' . $backtrace['line'] . '</>' . '<fg=white>' . ') ' . '</>');
                                    }
                                    ?>
                                    <?php
                                    if (array_key_exists('object', $backtrace)) {
                                        echo htmlspecialchars('<fg=bright-cyan>' . get_class($backtrace['object']) . '</>' . '<fg=white>' . '::' . '</>');
                                    }
                                    ?>
                                    <?php
                                    if (array_key_exists('function', $backtrace)) {
                                        echo htmlspecialchars('<fg=bright-cyan>' . $backtrace['function'] . '</>' . '<fg=white>' . '.' . '</>');
                                    }
                                    ?>
                                </li>
                            <?php
                            }
                            ?>
                        </ol>
                    </div>
                    <hr>
                </li>
            <?php } ?>
            <?php if (strlen($context) > 0) { ?>
                <li>
                    <span class="text-yellow mr-1">
                        <b>
                            <u>CONTEXT :</u>
                        </b>
                    </span>
                    <span class="mr-1 text-yellow"><?php echo strtoupper("\\{$context}") ?></span>
                    <br>
                </li>
            <?php } ?>

            <?php if (!is_null($content)) { ?>
                <li>
                    <span class="text-white mr-1">
                        <b>
                            <u>MESSAGE :</u>
                        </b>
                    </span>
                    <span class="text-white"><?php echo htmlspecialchars($content) ?></span>
                </li>
            <?php } ?>
        </ul>
    </div>
</section>
