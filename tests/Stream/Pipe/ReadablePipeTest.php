<?php
declare (strict_types = 1);

namespace Async\Stream\Pipe;

use PHPUnit\Framework\TestCase;

class ReadablePipeTest extends TestCase
{
    const UNIX_SOCKET_PATHNAME = __DIR__ . '/../../../run/readable-pipe-test1.unix';

    static $readable;

    static $writable;

    /**
     * @var ReadablePipe
     */
    static $readablePipe;

    static $writablePipe;

    public static function setUpBeforeClass()
    {
    }

    /**
     * @test
     */
    public function _()
    {
        $loop = uv_loop_new();

        socket_create_pair(AF_UNIX, SOCK_STREAM, STREAM_IPPROTO_IP, $sockets);
        list($read, $write) = $sockets;
        socket_set_nonblock($read);
        socket_set_nonblock($write);

        $readHandler = uv_pipe_init($loop, false);
        $writeHandler = uv_pipe_init($loop, false);

        uv_pipe_open($readHandler, (int)$read);
        uv_pipe_open($writeHandler, (int)$write);

        uv_write($writeHandler, 'hello world', function () {
            echo 'WRITE OK', PHP_EOL;
        });

        uv_read_start($readHandler, function ($a, $b, $c) {
            var_dump($a, $b, $c);

            uv_read_stop($a);
            uv_close($a);
        });

        uv_run($loop);
    }
}
