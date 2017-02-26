<?php
declare (strict_types = 1);

namespace Async\Http;

use Psr\Http\Message\StreamInterface;

final class HttpResponse
{
    /**
     * @var int
     */
    private $status;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var null|StreamInterface
     */
    private $body;

    /**
     * @var string
     */
    private $version;

    public function __construct(
        int $status = 200,
        array $headers = [],
        ?StreamInterface $body = null,
        string $version = '1.1'
    ) {
        $this->status = $status;
        $this->headers = $headers;
        $this->body = $body;
        $this->version = $version;
    }
}
