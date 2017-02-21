<?php
use Async\Loop\Timer;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = \Async\loop();
$stdout = $loop->stdout();

$loop->timer(0, 1, static function(Timer $timer) use ($stdout) {
    $a = 0;
    $closure = static function () use ($stdout) {
        $stdout->write('How are you?' . PHP_EOL);
    };

    while (++$a <= 1000) {
        $stdout->write('Hello World!' . PHP_EOL)->then($closure);
    }

    $timer->stop();
});

$loop->run();
