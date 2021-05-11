<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Class ErrorCodes.
 */
final class ErrorCodes
{
    public const DEFAULT_ERROR = 100;
    public const VALIDATION_ERROR = 101;
    public const NOT_FOUND = 104;
    public const UNAUTHORIZED = 401;
    public const NEED_OTHER_ACTION = 102;
    public const FORBIDDEN = 403;
    public const FAILED_RESULT = 103;
    public const WRONG_DATA = 406;

    const GUZZLE_CLIENT_EXCEPTION = 1001;
    const GUZZLE_SERVER_EXCEPTION = 1002;
    const GUZZLE_CONNECT_EXCEPTION = 1003;
}
