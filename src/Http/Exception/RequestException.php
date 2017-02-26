<?php
declare (strict_types = 1);

namespace Async\Http\Exception;

use Exception;

final class RequestException extends Exception
{
    private const REQUEST_START_LINE_TOO_LARGE = 1;
    private const REQUEST_START_LINE_INVALID = 2;
    private const REQUEST_HEADERS_TOO_LARGE = 3;
    private const REQUEST_HEADER_INVALID = 4;

    public static function requestStartLineTooLarge(int $maximumSize) : self
    {
        return new self(
            self::REQUEST_START_LINE_TOO_LARGE,
            sprintf('Request start line exceeded maximum size of %d bytes.', $maximumSize)
        );
    }

    public static function requestHeadersTooLarge(int $maximumSize) : self
    {
        return new self(
            self::REQUEST_HEADERS_TOO_LARGE,
            sprintf('Request headers exceeded maximum size of %d bytes.', $maximumSize)
        );
    }

    public static function requestStartLineInvalid() : self
    {
        return new self(
            self::REQUEST_START_LINE_INVALID,
            'Request start line is invalid'
        );
    }

    public static function requestHeaderInvalid() : self
    {
        return new self(
            self::REQUEST_HEADER_INVALID,
            'Request header invalid'
        );
    }
}
