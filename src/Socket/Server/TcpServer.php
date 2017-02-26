<?php
declare (strict_types = 1);

namespace Async\Socket\Server;

use Async\Socket\Socket;

final class TcpServer implements Server
{
    /**
     * @var resource
     */
    private $handler;
    /**
     * @var callable
     */
    private $closure;
    /**
     * @var
     */
    private $loop;

    /**
     * @var bool
     */
    private $closing = false;

    public function __construct($loop, callable $closure)
    {
        $this->handler = uv_tcp_init($loop);
        $this->closure = $closure;
        $this->loop = $loop;
    }

    public function bind(string $address, int $host)
    {
        uv_tcp_bind($this->handler, uv_ip4_addr($address, $host));
    }

    public function listen()
    {
        uv_listen($this->handler, 128, function ($server, $status) {
            $client = uv_tcp_init($this->loop);

            uv_accept($server, $client);

            $socket = new Socket($client);

            ($this->closure)($this, $socket);
        });
    }

    public function close()
    {
        if ($this->closing) {
            return;
        }

        $this->closing = true;
        uv_close($this->handler);
    }
}
