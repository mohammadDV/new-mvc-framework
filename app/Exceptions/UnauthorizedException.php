<?php

namespace App\Exceptions;

use Exception;

/**
 * Unauthorized Exception
 * 
 * Thrown when access is denied (403).
 */
class UnauthorizedException extends Exception
{
    /**
     * UnauthorizedException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = 'Access denied', int $code = 403, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return 403;
    }
}