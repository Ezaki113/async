<?php
declare (strict_types = 1);

use Async\Loop\Timer;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = new \Async\Loop\UvLoop();

$timer = $loop->timer(0, 500, static function (Timer $timer) use ($loop) {
    static $i = 0;

    if ($timer->isPending()) {
        echo 'Still pending', PHP_EOL;
    }

    if ($loop->isRunning()) {
        echo 'Loop still is running', PHP_EOL;
        $loop->stop();
    }

    if ($i > 2) {
        $timer->stop();
    }

    $i++;

    echo 'Timer works!', PHP_EOL;
});

$loop->run();
