<?php
declare (strict_types = 1);

namespace Async\Http\Parser;

use Async\Http\Exception\RequestException;
use Async\Http\HttpRequest;
use Async\Promise\Awaitable;
use Async\Promise\Coroutine;
use Async\Socket\Socket;

final class Http1Parser implements Parser
{
    private const MAX_START_LINE_LENGTH = 1024;
    private const MAX_HEADERS_LENGTH = 16384;

    private const START_LINE_PATTERN = "/^(GET|HEAD|POST|PUT|DELETE|CONNECT|OPTIONS|TRACE) (\S+) HTTP\/(1(?:\.\d+)?)\r\n$/i";

    public function parseRequest(Socket $socket) : Awaitable
    {
        return new Coroutine(static function () use ($socket) {
            $buffer = '';

            try {
                do {
                    $buffer .= yield $socket->read(self::MAX_START_LINE_LENGTH);
                } while (($position = strpos($buffer, "\r\n")) === false && strlen($buffer) < self::MAX_START_LINE_LENGTH);

                if ($position === false) {
                    throw RequestException::requestStartLineTooLarge(self::MAX_START_LINE_LENGTH);
                }

                $position += 2;

                $startLine = substr($buffer, 0, $position);
                $buffer = substr($buffer, $position);

                if (!preg_match(self::START_LINE_PATTERN, $startLine, $matches)) {
                    throw RequestException::requestStartLineInvalid();
                }

                list(
                    1 => $method,
                    2 => $path,
                    3 => $protocolVersion
                ) = $matches;

                $headers = [];
                $headersLength = 0;

                do {
                    while (($position = strpos($buffer, "\r\n")) === false) {
                        if (strlen($buffer) > self::MAX_HEADERS_LENGTH) {
                            throw RequestException::requestHeadersTooLarge(self::MAX_HEADERS_LENGTH);
                        }

                        $buffer .= yield $socket->read(self::MAX_HEADERS_LENGTH);
                    }

                    $position += 2;

                    $headerLine = substr($buffer, 0, $position);
                    $buffer = substr($buffer, $position);

                    if ($position === 2) {
                        break;
                    }

                    $headersLength += $position;
                    $delimiterPosition = strpos($headerLine, ':');
                    if ($delimiterPosition === false) {
                        throw RequestException::requestHeaderInvalid();
                    }

                    $name = rawurldecode(substr($headerLine, 0, $delimiterPosition));
                    $value = rawurldecode(trim(substr($headerLine, $delimiterPosition + 1)));

                    if(!isset($headers[$name])) {
                        $headers[$name] = [];
                    }

                    $headers[$name][] = $value;
                } while ($headersLength < self::MAX_HEADERS_LENGTH);

                if ($headersLength >= self::MAX_HEADERS_LENGTH) {
                    throw RequestException::requestHeadersTooLarge(self::MAX_HEADERS_LENGTH);
                }

                return new HttpRequest(
                    $method,
                    $path,
                    $headers,
                    $socket,
                    $protocolVersion
                );
            } finally {
                $socket->unshift($buffer);
            }
        });
    }
}
