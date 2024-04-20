<div class="py-1" style="border: 1px solid red">
    <?php if (isset($timestamp)) { ?>
        <span class="text-gray">
            <?php echo "[{$timestamp}]" ?>
        </span>
    <?php } ?>

    <?php if (isset($appName)) { ?>
        <span class="mx-1 px-1 bg-blue bg-<?php echo $bgColor ?> text-<?php echo $fgColor ?>"><?php echo strtoupper("{$appName}") ?></span>
    <?php } ?>

    <span class="px-1 bg-<?php echo $bgColor ?> text-<?php echo $fgColor ?>"><?php echo strtoupper($level) ?></span>
    <br>
    <span><?php echo htmlspecialchars($content) ?></span>
</div>
