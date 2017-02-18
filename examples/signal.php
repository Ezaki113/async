<?php
declare (strict_types = 1);

use Async\Loop\Signal;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = new \Async\Loop\UvLoop();

$timer = $loop->timer(0, 500, static function () use ($loop) {
    if ($loop->isRunning()) {
        echo 'Loop still is running', PHP_EOL;
    }
});

$signalCallback = static function (Signal $signal) use ($loop) {
    echo 'Signal incoming: ', $signal->getSigno(), PHP_EOL;

    $loop->stop();
};

$loop->signal(SIGTERM, $signalCallback);
$loop->signal(SIGINT, $signalCallback);

$loop->run();
