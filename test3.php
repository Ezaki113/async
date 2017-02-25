<?php
$m = memory_get_usage();

$loop = uv_loop_new();

for ($i = 0; $i < 1; $i++) {
    uv_async_send(uv_async_init($loop, static function ($async) {
        uv_close($async);
    }));
}

uv_run($loop, UV::RUN_DEFAULT);
uv_loop_delete($loop);
unset($loop);

echo memory_get_usage() - $m, PHP_EOL;
