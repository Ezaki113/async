<?php

use Async\Promise\Awaitable;
use Async\Promise\Coroutine;
use Async\Socket\Server\TcpServer;
use Async\Socket\Socket;

require_once __DIR__ . '/vendor/autoload.php';

$loop = \Async\loop();

$server = new TcpServer($loop->nativeHandler(), static function (TcpServer $server, Socket $socket) {
    static $count = 0;
    return (new Coroutine(static function () use ($socket) {
        static $response = "HTTP/1.1 200 OK\r\nContent-Length: 0\r\nContent-Type: text/html\r\nConnection: close\r\n\r\n";

        yield $socket->end($response);

        $socket->close();
    }))->then(static function () use (&$count, $server) {
        $count++;
        if ($count > 10000) {
            $server->close();
        }
    });
});

$server->bind('0.0.0.0', 8080);
$server->listen();

$loop->run();

