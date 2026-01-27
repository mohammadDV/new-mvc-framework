<?php

namespace App\Exceptions;

use Exception;

/**
 * Not Found Exception
 * 
 * Thrown when a resource is not found (404).
 */
class NotFoundException extends Exception
{
    /**
     * NotFoundException constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message = "Resource not found", int $code = 404, ?Exception $previous = null)
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
        return 404;
    }
}
