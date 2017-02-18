<?php
declare (strict_types = 1);

require_once __DIR__ . '/vendor/autoload.php';

$loop = \Async\loop();

$loop->queue(static function () {
    echo 'Hello Queue!', PHP_EOL;
});

$loop->timer(0, 1000, static function () {
    echo 'Hello Timer!', PHP_EOL;
});

$loop->run();
