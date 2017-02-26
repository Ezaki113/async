<?php

use Async\Http\{HttpRequest, HttpResponse};

require_once __DIR__ . '/vendor/autoload.php';

$loop = \Async\loop();

$server = new \Async\Http\HttpServer(new class implements \Async\Http\HttpRequestHandler {
    public function serve(HttpRequest $request): HttpResponse
    {

        die();
    }
});

$server->bind('0.0.0.0', 8080);
$server->listen();

$loop->run();

