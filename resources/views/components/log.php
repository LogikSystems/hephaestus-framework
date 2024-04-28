<section>
    <!-- <hr> -->
    <div class="bg-black text-white p-1">

        <span class="mx-1 text-<?php echo $bgColor ?>">
            <strong>
            <?php

            echo htmlspecialchars("<========={-----o")
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
            <span><?php echo strtoupper($appName . "-" . config('app.version')) ?></span>
        <?php } ?>

        <span class="mx-1 text-<?php echo $bgColor ?>">
            <?php
            echo "o----}=========>"
            ?>
        </span>
    </div>
    <div>
        <?php if (isset($context) && strlen($context) > 0) { ?>
            >
            <span class="mr-1 text-green"><?php echo strtoupper("{$context}") ?></span>
            <br>
        <?php } ?>
        <?php if (isset($content)) { ?>
            <span><?php echo htmlspecialchars($content) ?></span>
        <?php } ?>
    </div>
</section>
