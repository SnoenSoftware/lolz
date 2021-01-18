<?php

declare(strict_types=1);
/*
|--------------------------------------------------------------------------------------
|  Task File
|--------------------------------------------------------------------------------------
|
| This file basically registers a new task to be executed by Crunz
| To get the list of all frequency and constraint method, you may
| go to this link: https://github.com/lavary/crunz#scheduling-frequency-and-constraints
|
*/

use Crunz\Schedule;

$scheduler = new Schedule();
$task = $scheduler->run('bin/console feed:regenerate');
$task
    ->in(dirname(__DIR__))
    ->description('Generate lolz feed')
    ->preventOverlapping()
    ->everyMinute();

return $scheduler;
