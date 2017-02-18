<?php
declare (strict_types = 1);

require_once __DIR__ . '/vendor/autoload.php';

$loop = \Async\loop();

$loop->queue(static function () use ($loop) {
    $loop->stdout()->write('Hello Queue!' . PHP_EOL);
});

$loop->stdout()->write('Hello World!' . PHP_EOL);
$loop->stdout()->write('Hello World!' . PHP_EOL)
    ->then(static function () use ($loop) {
        $loop->stdout()->write('How are you?' . PHP_EOL);
    });$loop->stdout()->write('Hello World!' . PHP_EOL);
$loop->stdout()->write('Hello World!' . PHP_EOL);
$loop->stdout()->write('Hello World!' . PHP_EOL);
$loop->stdout()->write('Hello World!' . PHP_EOL);
$loop->stdout()->write('Hello World!' . PHP_EOL)
    ->then(static function () use ($loop) {
        $loop->stdout()->write('How are you?' . PHP_EOL);
    });

$loop->run();



