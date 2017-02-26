<?php
declare (strict_types = 1);

namespace Async\Http;

use Async\Http\Parser\Http1Parser;
use Async\Http\Parser\Parser;
use Async\Socket\Server\Server;
use Async\Socket\Server\TcpServer;
use Async\Socket\Socket;

final class HttpServer implements Server
{
    /**
     * @var Server
     */
    private $transport;

    /**
     * @var HttpRequestHandler
     */
    private $requestHandler;
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(HttpRequestHandler $requestHandler)
    {
        $this->transport = new TcpServer(
            \Async\loop()->nativeHandler(),
            function (Server $server, Socket $socket) {
                $this->accept($socket);
            }
        );
        $this->requestHandler = $requestHandler;
        $this->parser = new Http1Parser();
    }

    public function bind(string $address, int $host)
    {
        $this->transport->bind($address, $host);
    }

    public function listen()
    {
        $this->transport->listen();
    }

    private function accept(Socket $socket)
    {
        $request = $this->parser->parseRequest($socket);

        $this->requestHandler->serve($request);
    }
}
