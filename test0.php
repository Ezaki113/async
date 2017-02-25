<?php
// HTTP/1.1 200 OK\r\nContent-Length: 0\r\nContent-Type: text/html\r\nConnection: close\r\n\r\n
//require_once __DIR__ . '/vendor/autoload.php';

class TcpServer
{
    private $loop;
    private $tcp;

    private $counter = 0;

    public function __construct($loop)
    {
        $this->loop = $loop;
        $this->tcp = uv_tcp_init($loop);
    }

    public function bind(string $address, int $port)
    {
        uv_tcp_bind($this->tcp, uv_ip4_addr($address, $port));
    }

    public function listen()
    {
        uv_listen($this->tcp, 100, function ($server, $status) {

            $client = uv_tcp_init($this->loop);
            uv_accept($server, $client);

            $this->counter++;

            if ($this->counter > 1000) {
                uv_shutdown($this->tcp);
                uv_close($this->tcp);
            }

            uv_read_start($client, function ($socket, $nread, $buffer) {
                uv_close($socket);
                uv_read_stop($socket);
            });
        });
    }

    public function close()
    {
        if (get_resource_type($this->tcp) === 'uv') {
            uv_close($this->tcp);
        }
    }
}

$loop = uv_loop_new();

$tcpServer = new TcpServer($loop);
$tcpServer->bind('0.0.0.0', 9999);
$tcpServer->listen();

$m = memory_get_usage();
uv_run($loop, UV::RUN_DEFAULT);
echo memory_get_usage() - $m;
