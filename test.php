<?php

$sockets = stream_socket_pair(AF_UNIX, SOCK_STREAM, STREAM_IPPROTO_IP);
list($read, $write) = $sockets;

$loop = uv_loop_new();

$readHandler = uv_pipe_init($loop, false);
uv_pipe_open($readHandler, (int) $read);
uv_read_start($readHandler, function ($a, $b, $c) {
    var_dump($a, $b, $c);
});

$writeHandler = uv_pipe_init($loop, false);
uv_pipe_open($writeHandler, (int) $write);

uv_write($writeHandler, 'hello world');

uv_run($loop);
