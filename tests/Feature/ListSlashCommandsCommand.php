<?php

it('has bot:commands', function () {
    $this->artisan('bot:commands')->assertExitCode(0);
});
