<?php
declare (strict_types = 1);

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \Async\loop();

$loop->queue(static function () {
    echo 'Hello Queue!', PHP_EOL;
});

$loop->run();
