<?php
declare (strict_types = 1);

namespace Async\Http;

use Async\Stream\ReadableStream;

final class HttpRequest
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var ReadableStream|null
     */
    private $body;

    /**
     * @var string
     */
    private $protocolVersion;

    public function __construct(
        string $method,
        string $uri,
        array $headers = [],
        ?ReadableStream $body = null,
        string $protocolVersion = '1.1'
    ) {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return ReadableStream|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }
}
