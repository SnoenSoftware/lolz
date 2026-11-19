<?php

use Crunz\Schedule;

$schedule = new Schedule();
$schedule->run(PHP_BINARY.' -r '.escapeshellarg('file_put_contents(getenv("LOLZ_CRUNZ_MARKER"), "ran");'))
    ->preventOverlapping()
    ->everyMinute();

return $schedule;
