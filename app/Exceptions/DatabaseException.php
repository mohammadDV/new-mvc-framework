<?php

namespace App\Exceptions;

use Exception;
use PDOException;

/**
 * Database Exception
 * 
 * Wrapper for database-related exceptions.
 */
class DatabaseException extends Exception
{
    /**
     * DatabaseException constructor.
     *
     * @param string $message
     * @param PDOException|null $previous
     */
    public function __construct(string $message = 'Database operation failed', ?PDOException $previous = null)
    {
        $code = $previous ? $previous->getCode() : 500;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return 500;
    }
}