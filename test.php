<?php
require_once __DIR__ . '/vendor/autoload.php';

$loop = uv_loop_new();
$handler = uv_pipe_init($loop, false);
uv_pipe_open($handler, (int)STDIN);


uv_read_start($handler, function ($socket, $nread, $buffer) {
    var_dump($nread, $buffer);
});


uv_run($loop, \UV::RUN_DEFAULT);
uv_close($handler);
